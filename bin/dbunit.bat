@echo off
pushd .
cd %~dp0
cd "../vendor/EHER/PHPUnit/bin"
set BIN_TARGET=%CD%\dbunit
popd
php "%BIN_TARGET%" %*
