#!/bin/bash

cd www
ln -s ../../recipes_book .

docker-compose up --build -d
