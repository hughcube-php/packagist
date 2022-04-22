
ARG BASE_IMAGE=registry.cn-hangzhou.aliyuncs.com/hughcube/packagist:base-1733511-1

# 代码构建
FROM ${BASE_IMAGE} AS builder

# composer 慧哲私有仓库的账号密码
ARG COMPOSER_HZCUBE_USERNAME
ARG COMPOSER_HZCUBE_PASSWORD

# composer的构建环境变量
ENV COMPOSER_MEMORY_LIMIT -1
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_CACHE_DIR /root/.cache/composer
ENV COMPOSER_NO_INTERACTION 1
ENV COMPOSER_PROCESS_TIMEOUT 600
ENV COMPOSER_MAX_PARALLEL_HTTP 50
ENV COMPOSER_MAX_PARALLEL_HTTP 50

WORKDIR /app
COPY . .

# 使用阿里云镜像
#RUN composer config --global repositories.packagist composer https://mirrors.aliyun.com/composer/

# composer安装依赖
RUN composer install --prefer-dist --optimize-autoloader -vvv --profile --no-dev
RUN composer dump-autoload -o

# 禁止每次worker启动清除opCache(更改后需要通过php语法检测)
RUN sed -i "/->clearOpcodeCache()/d" "vendor/laravel/octane/src/Swoole/Handlers/OnWorkerStart.php"
#RUN cat "vendor/laravel/octane/src/Swoole/Handlers/OnWorkerStart.php"
RUN php -l "vendor/laravel/octane/src/Swoole/Handlers/OnWorkerStart.php"

# 优化项目(\Illuminate\Foundation\Console\OptimizeClearCommand)
RUN php artisan optimize:clear
RUN php artisan route:clear && php artisan route:cache
RUN php artisan config:clear && php artisan config:cache
#RUN php artisan event:clear && php artisan event:cache

# 创建数据表
RUN rm -rf "$APP_BASE_PATH/database/database.sqlite"
RUN touch "$APP_BASE_PATH/database/database.sqlite"
RUN php artisan migrate --force

# octane预处理
RUN php artisan octane:prepare --host="0.0.0.0" --port=80 --workers=2 --task-workers=2 --max-requests=1 --state-file="$OCTANE_STATE_FILE"

# 数据清理
RUN php artisan user:flush
RUN php artisan token:flush

# hugh.li
RUN php artisan user:create "local"
RUN php artisan token:create "local" --username="local" --abilities="*" --plain_text_token="local"

# 运行环境
FROM ${BASE_IMAGE}

COPY --from=builder /app /app
