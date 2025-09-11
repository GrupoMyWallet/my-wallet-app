from fastapi import FastAPI
from api.V1.endpoints import bancos

API_V1_STR: str = '/api/v1'

app = FastAPI(title="Analise de Arquivos")
app.include_router(bancos.router, prefix=API_V1_STR)

if __name__ == '__main__':
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8000, log_level="info",reload=True)
