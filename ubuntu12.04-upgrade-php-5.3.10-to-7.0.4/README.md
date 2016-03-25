# Ubuntu 12.04 升级 php 5.3.10 到 php 7.0.4

**1. 获取源码,准备编译**

    sudo a2dismod php5
    mkdir php7
    cd php7
    axel -a -n 10 http://cn2.php.net/distributions/php-7.0.4.tar.bz2
    tar -xjf php-7.0.4.tar.bz2
    cp -r php-7.0.4 php-7.0.4-cli
    mv php-7.0.4 php-7.0.4-apache2
    sudo apt-get install build-essential libxml2-dev apache2-prefork-dev

**2. 默认安装的情况**

1. 默认的扩展安装目录是 /usr/local/lib/php/extensions/no-debug-non-zts-20151012/, 我们需要调整为 /usr/lib/php7/20151012/
2. 默认cli安装路径是 /usr/local/bin/php, 由于 $PATH 里 /usr/local/bin 优先于 /usr/bin, 所以 make install 之后, php就已经是php7了,但是脚本里写明了 #!/usr/bin/php 的, 还是在用php5, /usr/bin/php其实就是个软链接,我们需要把它指向php7
3. 默认安装了/usr/local/bin/phpdbg, 我们不需要, --enable-phpdbg=no
4. 默认安装了/usr/local/bin/php-cgi, 我们不需要, --disable-cgi
5. 默认phpize的安装路径是/usr/local/bin/phpize, 同样由于 $PATH 里 /usr/local/bin 优先于 /usr/bin, 所以现在phpize已经是php7的了
6. 默认php-config的安装路径是/usr/local/bin/php-config, 同phpize, 现在php-config也已经是php7的了
7. 默认安装了pear, 我们不需要, --without-pear
8. 默认pdo已经启用了,我们想要编译成扩展加载, --disable-pdo
9. 默认不启用mysqlnd,我们编译pdo_mysql时需要它, --enable-mysqlnd

**3. 准备好配置文件目录**

    sudo mkdir -p /etc/php7/{apache2,cli} /etc/php7/conf.d
    cd /etc/php7/apache2
    sudo ln -s ../conf.d
    cd ../cli
    sudo ln -s ../conf.d

**4. 编译 php-cli**

    cd php-7.0.4-cli
    EXTENSION_DIR="/usr/lib/php7/20151012" ./configure \
        --enable-phpdbg=no \
        --disable-cgi \
        --without-pear \
        --disable-pdo \
        --enable-mysqlnd \
        --with-config-file-path=/etc/php7/cli \
        --with-config-file-scan-dir=/etc/php7/cli/conf.d \
        --enable-mbstring \
        --enable-exif \
        --enable-sockets \
        --with-openssl \
        --with-zlib
    make
    sudo make install
    sudo cp php.ini-production /etc/php7/cli/php.ini

**5. 编译 apache mod_php**

    cd php-7.0.4-apache2
    EXTENSION_DIR="/usr/lib/php7/20151012" ./configure \
        --enable-phpdbg=no \
        --disable-cgi \
        --without-pear \
        --disable-cli \
        --disable-pdo \
        --enable-mysqlnd \
        --with-apxs2=/usr/bin/apxs2 \
        --with-config-file-path=/etc/php7/apache2 \
        --with-config-file-scan-dir=/etc/php7/apache2/conf.d \
        --enable-mbstring \
        --enable-exif \
        --enable-sockets \
        --with-openssl \
        --with-zlib
    make
    sudo make install
    sudo cp php.ini-production /etc/php7/apache2/php.ini

**6. 修改 php.ini 默认配置**

    include_path = ".:/usr/share/php"
    error_reporting = E_ALL
    track_errors = On

    # sudo mkdir -m 0777 /var/log/php7
    # php-cli
    error_log = /var/log/php7/cli.log
    memory_limit = 512M

    # php-apache2
    error_log = /var/log/php7/apache2.log

**7. 让apache开始处理php请求**

    cat /etc/apache2/mods-available/php7.conf
    <IfModule mod_php7.c>
        <FilesMatch "\.ph(p|tml)$">
            SetHandler application/x-httpd-php
        </FilesMatch>
    </IfModule>

    # 由于 make install 时会启用php.load,但那时还没有php.conf,所以先禁用一下
    sudo a2dismod php7
    sudo a2enmod php7
    sudo service apache2 restart

**8. 其它扩展的安装方法**

    # 以pdo, pdo_mysql为例
    sudo apt-get install autoconf
    cd php-7.0.4-apache2/ext/pdo/
    phpize
    ./configure
    make
    sudo make install
    cd ../pdo_mysql/
    phpize
    ./configure
    make
    sudo make install
    # 在 /etc/php7/conf.d 里加上pdo.ini, pdo_mysql.ini, cli和apache里就都用上gmp扩展了
    cat /etc/php7/conf.d/pdo.ini
    extension=pdo.so
    cat /etc/php7/conf.d/pdo_mysql.ini
    extension=pdo_mysql.so

**9. 几个有用的链接**

- https://httpd.apache.org/docs/current/mod/mod_so.html
- https://httpd.apache.org/docs/current/dso.html
- http://php.net/manual/en/intro.mysqlnd.php

**10. 扩展安装记录**

    cd pdo_sqlite
    phpize
    ./configure
    make
    sudo make install

    cd ../curl/
    sudo apt-get install libcurl4-openssl-dev
    phpize
    ./configure
    make
    sudo make install

    cd ../gd/
    sudo apt-get install libpng12-dev libjpeg-dev
    phpize
    ./configure
    make
    sudo make install

    cd ../gmp/
    sudo apt-get install libgmp-dev
    phpize
    ./configure
    make
    sudo make install

    cd ../imap/
    sudo apt-get install libc-client2007e-dev
    phpize
    ./configure --with-kerberos --with-imap-ssl
    make
    sudo make install

    ce ../mcrypt/
    # This extension rely in libmcrypt which is dead, unmaintained since 2007.
    # Please don't rely on it, consider switching to well maintained alternatives (openssl, crypt, password hashing functions, phpseclib, password_compat...)
    sudo apt-get install libmcrypt-dev
    phpize
    ./configure
    make
    sudo make install

    cd ../soap/
    phpize
    ./configure
    make
    sudo make install

    cd ../zip/
    phpize
    ./configure
    make
    sudo make install

    wget https://pecl.php.net/get/xdebug -O xdebug.tgz
    tar -xzf xdebug.tgz
    cd xdebug-2.4.0/
    phpize
    ./configure
    make
    sudo make install

    git clone https://github.com/heguangyu5/bloomy.git
    cd bloomy
    phpize
    ./configure
    make
    sudo make install

    wget http://www.xunsearch.com/scws/down/scws-1.2.3.tar.bz2
    tar -xjf scws-1.2.3.tar.bz2
    cd scws-1.2.3/
    ./configure --prefix=/usr/local/scws
    make
    sudo make install
    wget http://www.xunsearch.com/scws/down/scws-dict-chs-utf8.tar.bz2
    tar -xjf scws-dict-chs-utf8.tar.bz2
    sudo mv dict.utf8.xdb /usr/local/scws/etc/
    cd phpext/
    phpize
    ./configure --with-scws=/usr/local/scws
    make
    sudo make install

**11. 编译过程中遇到的问题**

1. 如果先 ./configure && make && sudo make install, 然后再 ./configure --with-config-file-path=XXX --with-config-file-scan-dir=XXX, 最终编译出来后, config-file-path 还是不对. **解决办法就是重新解压一个源码目录, configure 时一次把要加的选项都加上, 就没问题了.** 可能 make clean 也可以, 没试过.

2. 编译cli时不能 --disable-session, 如果disable了, 加载soap时会报错 undefined symbol: ps_globals
