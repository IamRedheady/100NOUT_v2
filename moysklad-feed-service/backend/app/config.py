from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8", extra="ignore")

    database_url: str = "postgresql://feed:feed@localhost:5432/feeddb"
    redis_url: str = "redis://localhost:6379/0"
    fernet_key: str = ""  # generate: python -c "from cryptography.fernet import Fernet; print(Fernet.generate_key().decode())"

    api_prefix: str = "/api"
    cors_origins: str = "http://localhost:5173,http://127.0.0.1:5173"

    ms_api_base: str = "https://api.moysklad.ru/api/remap/1.2"
    # Публичный URL API (для подсказки ссылки на YML в UI и картинок в YML)
    public_base_url: str = "http://localhost:8000"
    # Папка для хранения загружаемых изображений товаров
    media_dir: str = "/app/media"


settings = Settings()
