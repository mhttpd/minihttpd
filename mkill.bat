@ECHO OFF

:: Kill the running server process (non-cli) and any spawned FastCGI processes
taskkill /F /T /IM mhttpd.exe
