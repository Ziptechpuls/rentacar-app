FROM php:8.2-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    nginx supervisor \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pdo_pgsql pgsql \
    && docker-php-ext-enable pdo_pgsql pgsql

# Node.jsとnpmをインストール
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Composer インストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# アプリケーションの全ファイルをコピー
COPY src/. /var/www

# composer install（本番用）
RUN cd /var/www && composer install --no-dev --optimize-autoloader

# npm installとビルド（本番環境用）
RUN cd /var/www && npm install && npm run build
RUN chown -R www-data:www-data /var/www/public/build

# storageとbootstrap/cacheのパーミッション修正
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# public/storageのシンボリックリンクを削除し、正しいリンクを作成
RUN rm -f /var/www/public/storage && cd /var/www && php artisan storage:link || true

# マイグレーションを実行
RUN cd /var/www && php artisan migrate --force || true

# APP_KEYを設定（環境変数で上書き可能）
ENV APP_KEY=base64:bKgR/552hTzM1F7jDXIcr9Yt/YXUIKJ4uyIdMZN0spc=
ENV APP_ENV=production
ENV APP_DEBUG=true
ENV LOG_CHANNEL=stderr
ENV DB_CONNECTION=pgsql
ENV DB_HOST=aws-0-ap-northeast-1.pooler.supabase.com
ENV DB_PORT=6543
ENV DB_DATABASE=postgres
ENV DB_USERNAME=postgres.fzgjparlyawglrjwrldr
ENV DB_PASSWORD=Ziptech098!@
ENV SESSION_DRIVER=file
ENV CACHE_STORE=file

# 既存のnginx設定ファイルを削除
RUN rm -f /etc/nginx/conf.d/*
# sites-enabledディレクトリも削除
RUN rm -rf /etc/nginx/sites-enabled/*

# nginx設定ファイルを上書きコピー
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Supervisor設定ファイルを追加
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# php-fpmのエラーログを標準出力に出す
RUN sed -i 's|^;error_log = log/php-fpm.log|error_log = /dev/stderr|' /usr/local/etc/php-fpm.conf
# php-fpmの設定を修正
RUN sed -i 's|^listen = 127.0.0.1:9000|listen = 9000|' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's|^;listen.owner = www-data|listen.owner = www-data|' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's|^;listen.group = www-data|listen.group = www-data|' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's|^;listen.mode = 0660|listen.mode = 0660|' /usr/local/etc/php-fpm.d/www.conf

# 作業ディレクトリ
WORKDIR /var/www

# ポート80を公開
EXPOSE 80

# supervisordを起動
CMD ["/usr/bin/supervisord"]
