.PHONY: infra-clean infra-shell-php infra-stop infra-up xdebug-disable xdebug-enable \
install

default: help
test_file?="--exclude-group=Integration"
env?=dev
options?=

help:
	@grep -E '^[a-zA-Z_-]+:.*?##.*$$' $(MAKEFILE_LIST) | sort | awk '{split($$0, a, ":"); printf "\033[36m%-30s\033[0m %-30s %s\n", a[1], a[2], a[3]}'
#
# Executes a command in a running container, mainly useful to fix the terminal size on opening a shell session
#
# $(1) the options
#
define infra-shell
	docker-compose exec -e COLUMNS=`tput cols` -e LINES=`tput lines` $(1)
endef

# Make sure to run the given command in a container identified by the given service
#
# $(1) the user with whom to run the command
# $(2) the Docker Compose service
# $(3) the command to run
#
define run-in-container
	@if [ ! -f /.dockerenv -a "$$(docker-compose ps -q $(2) 2>/dev/null)" ]; then \
		if [ $$(env|grep -c "^CI=") -eq 0 ]; then \
			docker exec -it --user $(1) $$(docker-compose ps -q $(2)) /bin/sh -c "$(3)"; \
		else \
			docker-compose exec -T --user $(1) $(2) /bin/sh -c "$(3)"; \
		fi \
	else \
		$(3); \
	fi
endef

# Run php command in the php container
#
# $(1) the command to run
#
define run-php
	@$(call run-in-container,www-data,php,php -dmemory_limit=-1 $(1))
endef


########################################
#                APP                   #
########################################
install: ## SetUp the project
	@$(call run-php,composer install)
	@$(call run-php,bin/console doctrine:database:create)
	@$(call run-php,bin/console doctrine:schema:update --force)
	@$(call run-php,bin/console doctrine:fixtrues:load)

########################################
#              INFRA                   #
########################################

infra-clean: ## to stop and remove containers, networks, images
	@docker-compose down --rmi all

infra-shell-php: ## to open a shell session in the php container
	@$(call infra-shell, -u www-data php_fpm sh)

infra-shell-node: ## to open a shell session in the php container
	@$(call infra-shell,node sh)

infra-stop: ## to stop all the containers
	@docker-compose stop

infra-up: ## to start all the containers
	@if [ ! -e .env ]; then cp .env.dist .env; fi
	@rm -Rf var/{cache,logs,sessions}
	@mkdir -m 755 var/{cache,logs,sessions}
	@docker-compose up --build -d
	@docker-compose exec php_fpm sh -c "chown -R www-data var"
