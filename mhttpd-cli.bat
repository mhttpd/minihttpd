@ECHO OFF

:: Check running processes
tasklist | find "mhttpd.exe" > nul
if %ERRORLEVEL%==0 (
	echo MiniHTTPD Server is already running ...
	goto :end
)

:: Set the working directory to this file's
set MHTTPD_ROOT=%~dp0

:: Change to the working directory
pushd %MHTTPD_ROOT%

:: Start the server (using full paths)
"%MHTTPD_ROOT%bin\php\mhttpd-cli.exe" -f "%MHTTPD_ROOT%lib\minihttpd\launch.php" -c "%MHTTPD_ROOT%bin\php\mhttpd-cli.ini" -- %*

:: Kill any FCGI processes
echo Killing all FastCGI processes ...
taskkill /F /IM php-fcgi.exe

:: Return to the starting directory
popd

:end