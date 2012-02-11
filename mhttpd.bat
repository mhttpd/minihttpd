@ECHO OFF

:: Check running processes
tasklist | find "mhttpd.exe" > nul
if %ERRORLEVEL%==0 (
	echo MiniHTTPD Server is already running in the background
	goto :end
)

:: Set the MHTTPD root directory to this file's
set MHTTPD_ROOT=%~dp0

:: Change to the php directory
pushd "%MHTTPD_ROOT%bin\php"

echo Starting MiniHTTPD Server in the background ... 

:: Start the server
start /B mhttpd.exe

:: Return to the working directory
popd

echo -- done, use mkill.bat to shut down the server
echo Please wait for the browser to launch ...

:end