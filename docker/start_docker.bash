#!/bin/bash

#~ cd www
#~ ln -s ../../recipes_book .

docker-compose -p recipes_book up --build -d
