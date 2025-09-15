@echo off
echo Building KL Gestor Pub Docker images...
if "%1"=="" (
    bash docker-build.sh development
) else (
    bash docker-build.sh %1 %2 %3
)
