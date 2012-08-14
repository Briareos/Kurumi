@echo off
pushd .
cd %~dp0
cd "../vendor/EHER/PHPUnit/bin"
set BIN_TARGET=%CD%\phpcov
popd
php "%BIN_TARGET%" %*
