import os
from fastapi import FastAPI, Header, HTTPException, Depends
from api.V1.endpoints import bancos
import uvicorn

API_V1_STR: str = '/api/v1'
API_KEY = os.getenv('PYTHON_API_KEY')

if not API_KEY:
    raise ValueError("PYTHON_API_KEY não configurada no ambiente")

app = FastAPI(title="Processador de arquivos")

# Dependência para verificar a API Key
async def verify_api_key(x_api_key: str = Header(..., description="API Key para autenticação")):
    """
    Valida a API Key enviada no header X-API-Key
    """
    if x_api_key != API_KEY:
        raise HTTPException(
            status_code=403, 
            detail="API Key inválida ou não fornecida"
        )
    return x_api_key

# Rota de verificação de saúde da api
@app.get("/health")
async def health_check():
    return {"status": "healthy", "version": "1.0"}

# Inclui o roteador de bancos com a dependência de verificação da API Key
app.include_router(bancos.router, prefix=API_V1_STR, dependencies=[Depends(verify_api_key)])

if __name__ == '__main__':
    uvicorn.run("main:app", host="0.0.0.0", port=8000, log_level="info",reload=True)
