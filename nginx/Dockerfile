FROM nginx:1.15.6

ARG WEB_USER_ID=33
ARG WEB_USER_NAME=www-data
RUN echo "Building for web user: id=${WEB_USER_ID} name=${WEB_USER_NAME}"
RUN useradd -m -u ${WEB_USER_ID} ${WEB_USER_NAME} || echo "User exists, it's ok."
RUN sed -i -- "s/user nginx;/user ${WEB_USER_NAME};/" /etc/nginx/nginx.conf

COPY site.conf /etc/nginx/conf.d/default.conf

RUN mkdir /sock && chown ${WEB_USER_ID} /sock
