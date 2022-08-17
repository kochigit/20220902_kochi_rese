# 2020/03/13 時点での推奨版 12.16.1のnode.js使用
# 軽量らしいのでOSは、Alpine Linuxを選択
# https://hub.docker.com/_/node/
FROM node:16-alpine

# 環境変数
ENV LANG C.UTF-8
ENV TZ Asia/Tokyo
ENV APP_HOME /app
ENV PATH ${APP_HOME}/node_modules/.bin:$PATH
ENV HOST 0.0.0.0
ENV PORT 3000

WORKDIR ${APP_HOME}
ADD nuxt ${APP_HOME}

# apk アップデート
RUN apk update \
  && apk upgrade \
  && yarn global add @vue/cli \
  && yarn install \
  && rm -rf /var/cache/apk/*

EXPOSE 3000

CMD ["yarn", "dev"]