# ---------- Stage 1: build dependencies ----------
FROM python:3.12-slim AS builder

# Prevenção de prompts e timezone
ENV DEBIAN_FRONTEND=noninteractive

# Instala dependências de sistema necessárias (ajuste conforme necessidade)
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      build-essential gcc libffi-dev libssl-dev curl unzip libaio1 && \
    rm -rf /var/lib/apt/lists/*

# Cria diretório da app
WORKDIR /app

# Copia requirements (melhor ter um requirements.txt gerado)
COPY requirements.txt ./requirements.txt

# Cria ambiente virtual para reduzir tamanho em runtime
ENV VIRTUAL_ENV=/opt/venv
RUN python -m venv $VIRTUAL_ENV
ENV PATH="$VIRTUAL_ENV/bin:$PATH"

# Instala as dependências
RUN pip install --upgrade pip
RUN pip install --no-cache-dir -r requirements.txt

# ---------- Stage 2: runtime ----------
FROM python:3.12-slim AS runtime

ENV DEBIAN_FRONTEND=noninteractive
# Dependências de sistema em runtime (se necessário)
RUN apt-get update && \
    apt-get install -y --no-install-recommends libaio1 && \
    rm -rf /var/lib/apt/lists/*

# Cria usuário não-root
RUN useradd --create-home appuser
WORKDIR /home/appuser/app
COPY --from=builder /opt/venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

# Copia o código da aplicação
COPY . .

# Ajuste a porta (FastAPI padrão 8000)
EXPOSE 8000

# Variáveis de ambiente recomendadas
ENV PYTHONUNBUFFERED=1
ENV PYTHONDONTWRITEBYTECODE=1

# Muda para usuário não-root
USER appuser

# Executa a aplicação FastAPI
# Como o arquivo é main.py dentro do diretório de trabalho, o módulo é "main:app"
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]

