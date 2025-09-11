import re, os, io
from typing import Optional
import pandas as pd
from datetime import datetime
from fastapi import UploadFile, HTTPException, status



def extrair_data_hora(detalhes: str) -> Optional[pd.Timestamp]:
    try:
        # Procura padrão dd/mm hh:mm ou dd/mm/yyyy hh:mm
        padrao_com_ano = r"(\d{2}/\d{2}/\d{4}) (\d{2}:\d{2})"
        padrao_sem_ano = r"(\d{2}/\d{2}) (\d{2}:\d{2})"

        match = re.search(padrao_com_ano, detalhes)
        if match:
            data_str = match.group(1) + " " + match.group(2)
            return pd.to_datetime(data_str, format="%d/%m/%Y %H:%M")

        match = re.search(padrao_sem_ano, detalhes)
        if match:
            # Se não tem ano, adiciona o ano atual
            data_str = match.group(1) + f"/{datetime.now().year} " + match.group(2)
            return pd.to_datetime(data_str, format="%d/%m/%Y %H:%M")

    except Exception:
        return None
    return None

def csv_brasil_para_dict(df: pd.DataFrame)-> list[dict]:
    df = df.rename(columns={
        "Data": "data",
        "Lançamento": "descricao",
        "Detalhes": "detalhes",
        "N° documento": "numero_documento",
        "Valor": "valor",
        "Tipo Lançamento": "tipo_lancamento"
    })

    required_cols = ["data", "descricao", "detalhes", "numero_documento", "valor", "tipo_lancamento"]
    for col in required_cols:
        if col not in df.columns:
            df[col] = None

    # Filtra linhas que NÃO contém "Saldo" na coluna "descricao"
    df = df[~df["descricao"].astype(str).str.replace(" ", "", regex=False).str.contains("saldo", case=False,
                                                                                        na=False)]

    # VALOR COMO FLOAT NORMAL
    df["valor"] = pd.to_numeric(
        df["valor"].astype(str).str.replace(".", "", regex=False).str.replace(",", ".", regex=False),
        errors="coerce"
    ).fillna(0.0)

    # Convert numero_documento to str and handle NaN
    df["numero_documento"] = df["numero_documento"].astype(str)
    df["numero_documento"] = df["numero_documento"].replace("nan", None)

    # Extrai data como datetime, então formata para strig
    df["data"] = df.apply(lambda row: extrair_data_hora(str(row.get("detalhes", ""))), axis=1)

    def format_date(dt):
        if pd.isna(dt) or dt is None:
            return None
        return dt.strftime("%d/%m/%Y %H:%M")

    df["data"] = df["data"].apply(format_date)

    # Limpa a coluna "detalhes" removendo a data e hora
    df["detalhes"] = df["detalhes"].astype(str).str.replace(r"^\d{2}/\d{2}(?:/\d{4})?\s+\d{2}:\d{2}\s*", "", regex=True)

    records = []
    for _, row in df.iterrows():
        record = {
            "data": row["data"],
            "descricao": str(row["descricao"]),
            "detalhes": str(row["detalhes"]),
            "numero_documento": row["numero_documento"],
            "valor": float(row["valor"]),
            "tipo_lancamento": row["tipo_lancamento"]
        }
        records.append(record)

    return records


ALLOWED_CSV_MIMES = {"text/csv", "application/csv", "text/plain"}
ALLOWED_XLSX_MIMES = {"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"}

def criar_df_brasil(file: UploadFile, content: bytes) -> pd.DataFrame:
    # tenta inferir pela extensão como fallback
    filename = (file.filename or "").lower()
    ext = os.path.splitext(filename)[1]

    # CSV
    if file.content_type in ALLOWED_CSV_MIMES or ext == ".csv":
        decoded = content.decode("latin-1")
        try:
            df = pd.read_csv(io.StringIO(decoded), sep=",", on_bad_lines="skip", quotechar='"', encoding="latin-1")
            # se só tiver uma coluna, tenta com ;
            if df.shape[1] == 1:
                df = pd.read_csv(io.StringIO(decoded), sep=";", on_bad_lines="skip", quotechar='"', encoding="latin-1")
            return df
        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Erro ao ler arquivo CSV"
            ) from e

    # XLSX
    if file.content_type in ALLOWED_XLSX_MIMES or ext in (".xlsx", ".xlsm", ".xlsb"):
        try:
            try:
                return pd.read_excel(io.BytesIO(content), sheet_name="Extrato Conta", engine="openpyxl")
            except ValueError:
                return pd.read_excel(io.BytesIO(content), sheet_name=0, engine="openpyxl")
        except Exception as e:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Erro ao ler arquivo XLSX"
            ) from e

    raise HTTPException(
        status_code=status.HTTP_415_UNSUPPORTED_MEDIA_TYPE,
        detail="Apenas arquivos CSV e XLSX são suportados"
    )
