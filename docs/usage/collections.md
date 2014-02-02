# Collections

Collections are merely a wrapper for an array of objects, but has a bunch of other
useful methods to help you pluck items out of the array.

## Collection Methods

Below we'll reference a few of the many useful methods available within the
Laravel [`Collection`](https://github.com/laravel/framework/blob/master/src/Illuminate/Support/Collection.php) class.

### first()

The `first() method can be used to retrieve the first element in the collection.
This will be the first element contained within the collection internal array.

	$item = Cart::items()->first();

### last()

The `last()` method does the opposite of the `first()` method and returns the
last element contained within the collection internal array.

	$item = Cart::items()->last();

### isEmpty()

The `isEmpty()` method can be used to check whether or not the collection has
elements within it. It accepts no value and returns a boolean.

	if ( ! Cart::items()->isEmpty())
	{
		echo 'We have items on our cart :)';
	}
	else
	{
		echo 'Cart is empty';
	}

### count()

The `count()` method counts the number of items in the collection.

	$total = Cart::items()->count();
