# MiniHTTPD PHP Webserver 

This is a barebones webserver written entirely in PHP for standalone use on 
Windows systems. It is designed either for local testing of PHP projects on 
the fly without needing a full WAMP installation, or for running simple 
desktop applications via a browser-based user interface. 

Some of the main features include: 

- Simple installation, easy to configure and use OOB
- Console or windowed (background) mode
- Support for serving both static and dynamic content
- FastCGI process pool for executing PHP scripts
- Handling of multiple client connections
- Modular request handler system, easily extended
- Support for SSL connections
- Extensible source scripts 

## Installation 

Everything needed to run the webserver is included in the release. There are 
no external dependencies, and there is no installer. Just download the zip 
file and extract it to a folder of your choice - somewhere outside of any 
UAC control - and run the server from there. The binaries are included in 
the \bin\php directory (NB: if you're pulling from the repository, you'll 
need to add these manually from the zip). These are the standard PHP 5.3.3 
builds apart from mhttpd.exe, which is a stub created using phpack for 
running the server in windowed mode (php-win.exe doesn't work quite as 
expected here). Apart from the short script that's packed in this stub, all 
of the scripts that run the server are included in the \lib\minihttpd 
directory, where they can be edited or extended as required. 

There are a few configuration files that need to be read carefully and 
edited: 

- **\lib\minihttpd\default.ini**: this is the default server configuration 
file that is used if no ini file is found in the root folder. Copy this to 
the root folder, rename it (to anything) and make your changes. The options 
are quite simple, and should be well enough documented to be easily 
understood ;). 

- **\bin\php\config.ini**: this is the PHP configuration file for running 
the server in windowed mode (i.e. for the mhttpd.exe stub). It can't be 
renamed, and includes only the most basic PHP settings needed to run the 
server (e.g. loading the required extensions). 

- **\bin\php\mhttpd-cli.ini**: this is the PHP configuration file for 
running the server in console mode. It includes the full list of PHP 
settings from the 5.3.3 release, and loads the required extensions for the 
server. 

- **\bin\php\php-cgi.ini**: this contains the PHP configuration for the 
FastCGI processes. It should be edited to support whatever PHP scripts are 
to be served - particularly to load any extensions required by user scripts. 

Once these files have been edited, start the server in one of two ways: 

1. Run **mhttpd.bat** in the root folder to start the server in the 
background and optionally launch the default web browser. 

2. Run **mhttpd-cli.bat** to start the server with a console to monitor 
connections, any errors and optional debugging info. 

Any files or scripts to be served should be placed in either the default 
docroot (\www) or any other folder specified in the server configuration 
file. Currently there is no provision for configuring multiple docroots, 
i.e. a virtual server setup, and only one instance of the server can be run 
at a time. 

**IMPORTANT: Exposing the server to external connections has security 
implications and you do so at your own risk with this experimental 
application that has not been fully security-tested. Firewall the server 
port by default unless you really know what you're doing and understand the 
risks.** 

## How it works 

Creating standalone PHP applications for Windows systems is inherently 
problematic due to the lack of threading support or any equivalent of the 
process forking available in UNIX-based versions. There have been other fine 
PHP projects like Nanoweb that have tried to address the issue, but none of 
the solutions have seemed particularly satisfactory so far. 

The basic problem is this: how to handle multiple client connections and 
serve dynamic content in a single thread when user scripts could either kill 
the server itself simply by calling exit or die(), or hang the server while 
waiting for the scripts to execute? 

It turns out that it's possible to simulate multiple threads - from the 
perspective of actual webserver functionality - by making creative use of 
sockets and external FastCGI processes. The main server loop can queue 
concurrent client requests relatively easily (in this case, by using 
stream_select() on the main listening socket), and dispatch dynamic requests 
to external FastCGI processes without blocking. Responses from these 
processes can be handled in the main loop whenever they're done (again via 
stream_select()) and passed back to the client transparently. The main loop 
therefore needs to do relatively little work or waiting around, which means 
that decent concurrency is possible most of the time. Any script execution 
is also completely isolated from the server itself. 

The application includes a FastCGI manager that can spawn new processes 
dynamically up to the maximum configured number, and a FastCGI client class 
that handles all of the FCGI protocol communication via FCGI record objects. 
A typical request for a PHP script might therefore go something like this, 
with each stage handled in a different iteration of the main server loop 
controlled by socket_select(): 

1. The client (browser) requests a connection and is added to a free client 
slot. 
2. The client request is processed and dispatched to a free FastCGI process 
for execution. If none is available, the manager will try to spawn a new 
one, otherwise the request will be queued with the least busy process in the 
pool. 
3. The main server loop listens for any response on the FastCGI process 
socket while continuing to handle other client requests, and once it 
receives something it processes the response and sends it back to the 
client. 
4. The client connection to the server is closed and the slot is now free to 
accept a new connection. 

This isn't a completely asynchronous solution, and any large file downloads 
may still present a problem, but in everyday usage it allows for decent 
concurrency and the ability, for example, to make optimal use of Keep-Alive 
connections without choking the server. This is more than adequate for 
running a browser-based UI locally, or for handling a reasonable number of 
simultaneous external requests. 

# API Documentation 

The release includes basic documentation of the API via phpDocumentor. This 
can be accessed directly in \lib\minihttpd\www\docs, or browsed from the 
running server by navigating to schema://address:port/api-docs/ (see the 
main ini file for the authorizaton settings). 

Otherwise, the source code is relatively compact and hopefully transparent, 
so it shouldn't take too long to master the details ;). Functionality of the 
core classes can be extended quite easily by following the instructions in 
the notes for \lib\minihttpd\classes.php. 

*Copyright (c) 2010 MiniHTTPD Team* 

