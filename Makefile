TOP = $(realpath $(dir $(lastword $(MAKEFILE_LIST))))
CONTAINER_NAME = phpunit_ldap
CONTAINER_PORT = 1389

test:
	$(TOP)/vendor/bin/phpunit -c phpunit.xml.dist --exclude-group ldap_integration

integration_tests: | start_docker_ldap_server wait_docker_ldap_server
	$(TOP)/vendor/bin/phpunit -c phpunit.xml.dist --group ldap_integration

start_docker_ldap_server: | docker_stop_server docker_rm_server
	docker run --name $(CONTAINER_NAME) -p $(CONTAINER_PORT):389 \
		--detach \
		--volume $(TOP)/tests/assets/ldif:/container/service/slapd/assets/config/bootstrap/ldif/custom osixia/openldap:1.1.9 \
		--copy-service 

wait_docker_ldap_server:
	sleep 5

docker_rm_server:
	docker rm $(CONTAINER_NAME) || true

docker_stop_server:
	docker stop $(CONTAINER_NAME) || true
