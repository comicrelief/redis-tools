#!/usr/bin/env bash

set -e

authenticate(){
  cf api $CF_API
  cf auth $CF_USERNAME $CF_PASSWORD
}

create_orgs(){
  cf create-org comicrelief
  cf target -o comicrelief
}

create_services(){
  echo "No services to create"
}

create_services_fail(){
  echo "Services failed to create"
}

create_space(){
  cf create-space $CF_SPACE
  cf target -s $CF_SPACE
}

authenticate
create_orgs
create_space
create_services || create_services_fail
