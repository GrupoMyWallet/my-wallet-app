from fastapi import APIRouter
from fastapi import File, UploadFile
from typing import Optional
from pydantic import BaseModel
from fastapi import HTTPException, status
import logging
from api.V1.funcoes_banco import csv_brasil_para_dict, criar_df_brasil

router = APIRouter()



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

        if file.content_type in ["text/csv", "application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"]:

            df = criar_df_brasil(file, content)

            if df.empty:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="O arquivo enviado ou e do tipo errado ou não contém dados válidos."
                )

            return csv_brasil_para_dict(df)


    except Exception as e:
        logging.error(f"Erro ao processar arquivo: {e}", exc_info=True)
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Ocorreu um erro interno ao processar o arquivo"
        )

