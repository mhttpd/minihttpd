@ECHO OFF

:: Kill the running server process (non-cli)
echo Stopping the server ...
taskkill /F /IM mhttpd.exe

:: Kill any spawned FastCGI processes
echo Killing all FastCGI processes ...
taskkill /F /IM php-fcgi.exe