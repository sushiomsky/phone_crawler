FROM python:3.12-slim

WORKDIR /app
COPY pyproject.toml ./
COPY redflag_api ./redflag_api
RUN pip install --no-cache-dir .
EXPOSE 8080
CMD ["redflag-api", "serve", "--host", "0.0.0.0", "--port", "8080"]
