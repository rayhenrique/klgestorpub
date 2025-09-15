@echo off
echo Starting KL Gestor Pub with Docker...
if "%1"=="" (
    bash docker-start.sh development
) else (
    bash docker-start.sh %1
)
