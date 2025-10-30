from fastapi import FastAPI
from api.V1.endpoints import bancos
import uvicorn

API_V1_STR: str = '/api/v1'

app = FastAPI(title="Analise de Arquivos")

@app.get("/health")
async def health_check():
    return {"status": "healthy", "version": "1.0"}

app.include_router(bancos.router, prefix=API_V1_STR)

if __name__ == '__main__':
    uvicorn.run("main:app", host="0.0.0.0", port=8000, log_level="info",reload=True)
