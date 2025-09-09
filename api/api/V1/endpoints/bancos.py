from fastapi import APIRouter
from fastapi import File, UploadFile
from typing import Optional
from pydantic import BaseModel
from fastapi import HTTPException, status
import logging
import pandas as pd
import io, re
from datetime import datetime


router = APIRouter()


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


class LancamentoBB(BaseModel):
    data: Optional[str]  # Changed to str to match the output
    descricao: str
    detalhes: str
    numero_documento: Optional[str]  # Changed to str for consistency
    valor: float
    tipo_lancamento: Optional[str]


@router.post(
    "/brasil",
    summary="Extrato Banco do Brasil",
    description="Recebe um arquivo CSV do Banco do Brasil e retorna um JSON estruturado",
    response_model=list[LancamentoBB]
)
async def bb_file_to_json(file: UploadFile = File(...)):
    try:
        content = await file.read()
        decoded = content.decode("latin-1")
        print(file.content_type)
        if file.content_type in ["text/csv", "application/vnd.ms-excel"]:

            df = pd.read_csv(
                io.StringIO(decoded),
                sep=',',
                on_bad_lines='skip',
                quotechar='"',
                encoding='latin-1'
            )

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

            # Handle NaT and None properly before strftime
            def format_date(dt):
                if pd.isna(dt) or dt is None:
                    return None
                return dt.strftime("%d/%m/%Y %H:%M")

            df["data"] = df["data"].apply(format_date)

            # Limpa a coluna "detalhes" removendo a data e hora
            df["detalhes"] = df["detalhes"].astype(str).str.replace(r"^\d{2}/\d{2}(?:/\d{4})?\s+\d{2}:\d{2}\s*", "",
                                                                    regex=True)
            # Convert to dict, ensuring types match the model
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

        else:
            raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Arquivo do tipo inválido")

    except Exception as e:
        logging.error(f"Erro ao processar arquivo: {e}", exc_info=True)
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Ocorreu um erro interno ao processar o arquivo"
        )

