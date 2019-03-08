FROM centos:centos6.10

# Needed in the corporate development environment.
RUN echo 'sslverify=no' >> /etc/yum.conf && yum install -y epel-release

RUN yum install -y php php-bcmath php-cli php-common php-devel \
    php-gd php-intl php-ldap php-mbstring php-mcrypt php-mysql \
    php-pdo php-pear php-pecl-apc php-pecl-apc-devel php-pecl-memcache \
    php-pecl-memcached php-pecl-mongo php-process php-pecl-xdebug php-xml \
    && sed -ie 's/apc.enable_cli\s*=\s*.*/apc.enable_cli=1/' /etc/php.d/apc.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["/usr/bin/php"]