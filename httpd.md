# Apache httpd

## bin/apachectl

    load evnvars // LD_LIBRARY_PATH, 加上httpd自己的lib目录
    ulimit -S -n `ulimit -H -n` // man bash
                                // ulimit -n 用于修改允许打开的fd数量,-H 是 hard limit, -S 是 softlimit
                                // 这个命令把允许打开的fd数量提到最高

    # httpd的几种调用方式
    httpd -h
    httpd -k start|stop|restart|graceful|graceful-stop
    httpd -t

## cgi-bin

一个c语言的例子

    cat a.c
    #include <stdio.h>

    int main(int argc, char *argv[], char *env[])
    {
        printf("Content-Type: text/plain\n\n");

    	printf("argc = %d\n", argc);

    	printf("argv:\n");
    	int i;
    	for (i = 0; i < argc; i++) {
    		printf("argv[%d] = %s\n", i, argv[i]);
    	}

    	printf("evn:\n");
    	i = 0;
    	while (env[i]) {
    		printf("env[%d] = %s\n", i, env[i]);
    		i++;
    	}

    	return 0;
    }

    clang a.c
    http://localhost:8888/cgi-bin/a.out

## httpd.conf

    User daemon
    Group daemon
    // 使用普通用户启动 ./httpd -k start, 这里设的User/Group不起作用
    // 使用root用户启动, 才起作用. ps aux | grep httpd 可看到一个root用户的httpd进程和几个daemon用户的httpd进程

    AllowOverride   // Types of directives that are allowed in .htaccess files
                    // AllowOverride is valid only in <Directory> sections specified without regular expressions,
                    // not in <Location>, <DirectoryMatch> or <Files> sections.
                    // When this directive is set to None, then .htaccess files are completely ignored.
                    // In this case, the server will not even attempt to read .htaccess files in the filesystem.
                    // When this directive is set to All, then any directive which has the .htaccess Context is allowed in .htaccess files.

    Order Allow,Deny
    Order Deny,Allow    // Allow和Deny用来定义规则,Order用于处理规则之外的情况
                        // 一个请求如果仅匹配Deny,那就Deny,如果仅匹配Allow,那就Allow
                        // 如果既匹配Deny,又匹配Allow,那就看Order的最后一个是Deny还是Allow
                        // 如果哪个都不匹配,也看最后一个

    /* alias_module

        Redirect:
        Allows you to tell clients about documents that used to exist in your server's namespace, but do not anymore.
        The client will make a new request for the document at its new location.
        Redirect permanent /foo http://www.example.com/bar

        Alias:
        Maps web paths into filesystem paths and is used to access content that does not live under the DocumentRoot.
        Alias /webpath /full/filesystem/path

        ScriptAlias:
        This controls which directories contain server scripts.
        ScriptAliases are essentially the same as Aliases, except that documents in the target directory are treated as applications and run by the server when requested rather than as documents sent to the client.
        ScriptAlias /cgi-bin/ "/home/heguangyu5/my-httpd/cgi-bin/"
    */

    mime_module
    AddHandler cgi-script .cgi  // 把一个扩展名和一个handler关联起来,比如要想让cgi-bin/a.out在htdocs里也能运行
                                // 就得 cp cgi-bin/a.out htdocs/a.cgi

# 源码分析

    @see The Apache Modules Book: Application Development with Apache (Nick Kew)
    @see http://dev.ariel-networks.com/apr/apr-tutorial/html/apr-tutorial.html

    struct process_rec {
        apr_pool_t *pool;
        apr_pool_t *pconf;
        int argc;
        const char * const *argv;
        const char *short_name;
    }

    httpd的main入口定义在 server/main.c#0442
    init_process() 先创建一个 process pool, 又创建一个 pconf subpool, 然后从 argv[0] 里取得启动httpd命令里的文件名做为short_name,初始化好了 process_rec.
