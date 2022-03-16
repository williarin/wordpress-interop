CLI=docker compose run --rm wp-cli wp

.PHONY: install reset reset-containers reset-install reset-posts reset-woocommerce
install: reset-install reset-posts reset-woocommerce

reset: reset-containers reset-install reset-posts reset-woocommerce

reset-containers:
	@echo "Preparing containers..."
	@docker compose down -v || true
	@docker compose up -d

reset-install:
	@echo "Waiting 10 seconds for environment to be ready..."
	@sleep 10
	@echo "Installing WordPress..."
	@$(CLI) core install \
		--url="http://localhost" \
		--title="My Awesome WordPress Site" \
		--admin_user=admin \
		--admin_password=admin \
		--admin_email=contact@example.com \
		--skip-email
	@$(CLI) plugin install woocommerce --activate
	@$(CLI) plugin install wordpress-importer --activate

reset-posts:
	@echo "Creating some sample data..."
	@$(CLI) post create --user=1 --post_title='A post' --post-name='a-post' --post_content='Just a small post.' --meta_input='{"key1":"value1","key2":"value2"}' --post_status='publish'
	@$(CLI) post create --user=1 --post_title='Another post' --post-name='another-post' --post_content='Another small post.' --meta_input='{"key1":"value3","key3":"value4"}' --post_status='private'
	@$(CLI) post create --user=1 --post_title='Not sure if this should be published' --post-name='not-sure-if-this-should-be-published' --post_content='Should we publish this?' --post_status='draft'
	@$(CLI) media import /assets/images/featuredimage.png --user=1 --post_id=4 --title="A white banner" --featured_image

reset-woocommerce:
	@echo "Importing WooCommerce sample data and creating some more..."
	@$(CLI) import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=create
	@$(CLI) wc customer create --email='justin@woo.local' --user=1 --password='he llo' \
		--billing='{"first_name":"Justin","last_name":"Hills","company":"Google","address_1":"4571 Ersel Street","city":"Dallas","state":"Texas","postcode":"75204","country":"United States","email":"justin@woo.local","phone":"214-927-9108"}' \
		--shipping='{"first_name":"Justin","last_name":"Hills","company":"Google","address_1":"4571 Ersel Street","city":"Dallas","state":"Texas","postcode":"75204","country":"United States","email":"justin@woo.local","phone":"214-927-9108"}'
	@$(CLI) wc customer create --email='otis@woo.local' --user=1 --password='he llo' \
		--billing='{"first_name":"Ottis","last_name":"Bruen","company":"Facebook","address_1":"81 Spring St","city":"New York","state":"North Dakota","postcode":"10012","country":"United States","email":"ottis@woo.local","phone":"(646) 613-1367"}' \
		--shipping='{"first_name":"Ottis","last_name":"Bruen","company":"Facebook","address_1":"81 Spring St","city":"New York","state":"North Dakota","postcode":"10012","country":"United States","email":"ottis@woo.local","phone":"(646) 613-1367"}'
	@$(CLI) wc shop_order create --user=1 --customer_id=3 --line_items='[{"product_id":17},{"product_id":23}]'
	@$(CLI) wc shop_order create --user=1 --customer_id=4 --line_items='[{"product_id":24}]'
	@$(CLI) wc shop_order create --user=1 --customer_id=0 --line_items='[{"product_id":20},{"product_id":22}]' \
		--billing='{"first_name":"Trudie","last_name":"Metz","company":"Amazon","address_1":"135 Wyandot Ave","city":"Marion","state":"Ohio","postcode":"43302","country":"United States","email":"trudie@woo.local","phone":"(740) 383-4031"}' \
		--shipping='{"first_name":"Trudie","last_name":"Metz","company":"Amazon","address_1":"135 Wyandot Ave","city":"Marion","state":"Ohio","postcode":"43302","country":"United States","email":"trudie@woo.local","phone":"(740) 383-4031"}'
	@$(CLI) wc product_attribute create --name="Manufacturer" --user=1
	@$(CLI) wc product_attribute_term create 1 --name="SuperBrand" --user=1
	@$(CLI) wc product_attribute_term create 1 --name="MegaBrand" --user=1
	@$(CLI) wc product create --name="Special Forces Hoodie" --description="Military hoodie with logo" \
		--type=simple --regular_price=500 --user=1 --categories='[{"id": 17}]' --sku="super-forces-hoodie" \
		--attributes='[{"name": "pa_manufacturer", "visible": true, "options": ["MegaBrand"]}]'
	@$(CLI) post term add 64 pa_manufacturer megabrand

.PHONY: test
test:
	@./vendor/bin/phpunit
	@./vendor/bin/ecs check

.PHONY: fix
fix:
	@./vendor/bin/ecs check --fix
