CLI=docker compose run --rm wp-cli wp

reset: reset-install reset-posts

reset-install:
	@echo "Preparing containers..."
	@docker compose down -v || true
	@docker compose up -d
	@sleep 10
	@echo "Installing WordPress..."
	@$(CLI) core install \
		--url="http://localhost" \
		--title="My Awesome WordPress Site" \
		--admin_user=admin \
		--admin_password=admin \
		--admin_email=contact@example.com \
		--skip-email

reset-posts:
	@$(CLI) post create --post_title='A post' --post-name='a-post' --post_content='Just a small post.' --meta_input='{"key1":"value1","key2":"value2"}' --post_status='publish'
	@$(CLI) post create --post_title='Another post' --post-name='another-post' --post_content='Another small post.' --meta_input='{"key1":"value3","key3":"value4"}' --post_status='private'
	@$(CLI) post create --post_title='Not sure if this should be published' --post-name='not-sure-if-this-should-be-published' --post_content='Should we publish this?' --post_status='draft'
