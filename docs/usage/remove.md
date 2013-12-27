# Removing items {#remove-items}

## Remove a single item {#single-item}

	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da');


## Remove multiple items {#multiple-items}

Removing multiple items is easy and we provide you with two ways to accomplish this.

### Method 1

Pass in an array with the row id's you want to remove.

	Cart::remove(array(
		'027c91341fd5cf4d2579b49c4b6a90da',
		'56f0ab12a38f8317060d40981f6a4a93',
	));

### Method 2

Pass in multiple arguments, where each argument corresponds to an item row id.

	Cart::remove('027c91341fd5cf4d2579b49c4b6a90da', '56f0ab12a38f8317060d40981f6a4a93');
