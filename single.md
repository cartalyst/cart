<h1>Introduction</h1>

<p>A modern and framework agnostic shopping cart package featuring <a href="#instances">multiple instances</a>, <a href="#attributes">item attributes</a> and <a href="https://www.cartalyst.com/manual/conditions">Conditions</a>.</p>

<p>The package requires PHP 5.4+ and comes bundled with a Laravel 4 Facade and a Service Provider to simplify the optional framework integration and follows the FIG standard PSR-4 to ensure a high level of interoperability between shared PHP code and is fully unit-tested.</p>

<p>Have a <a href="#installation">read through the Installation Guide</a> and on how to <a href="#laravel-4">Integrate it with Laravel 4</a>.</p>

<h3>Quick Example</h3>

<h4>Add a single item to the cart</h4>

<pre class="prettyprint lang-php"><code>Cart::add([
    'id'       =&gt; 'tshirt',
    'name'     =&gt; 'T-Shirt',
    'quantity' =&gt; 1,
    'price'    =&gt; 12.50,
]);
</code></pre>

<h4>Add multiple items to the cart</h4>

<pre class="prettyprint lang-php"><code>Cart::add([

    [
        'id'       =&gt; 'tshirt',
        'name'     =&gt; 'T-Shirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 12.50,
    ],

    [
        'id'       =&gt; 'sweatshirt',
        'name'     =&gt; 'Sweatshirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 98.32,
    ],

]);
</code></pre>

<h4>Get all the cart items</h4>

<pre class="prettyprint lang-php"><code>$items = Cart::items();
</code></pre><h1>Installation</h1>

<p>The best and easiest way to install the Cart package is with <a href="http://getcomposer.org">Composer</a>.</p>

<h2>Preparation</h2>

<p>Open your <code>composer.json</code> file and add the following to the <code>require</code> array:</p>

<pre><code>"cartalyst/cart": "1.0.*"
</code></pre>

<p>Add the following lines after the <code>require</code> array on your <code>composer.json</code> file:</p>

<pre><code>"repositories": [
    {
        "type": "composer",
        "url": "http://packages.cartalyst.com"
    }
]
</code></pre>

<blockquote>
<p><strong>Note:</strong> Make sure your <code>composer.json</code> file is in a valid JSON format after applying the required changes.<br>
<em>You can use the <a href="http://jsonlint.com/">JSONLint</a> online tool to validate your <code>composer.json</code> file.</em></p>
</blockquote>

<h2>Install the dependencies</h2>

<p>Run Composer to install or update the new requirement.</p>

<pre><code>php composer install
</code></pre>

<p>or</p>

<pre><code>php composer update
</code></pre>

<p>Now you are able to require the <code>vendor/autoload.php</code> file to autoload the package.</p><h1>Integration</h1>

<h2>Laravel 4</h2>

<p>The Cart package has optional support for Laravel 4 and it comes bundled with a Service Provider and a Facade for easy integration.</p>

<p>After installing the package, open your Laravel config file located at <code>app/config/app.php</code> and add the following lines.</p>

<p>In the <code>$providers</code> array add the following service provider for this package.</p>

<pre><code>'Cartalyst\Cart\Laravel\CartServiceProvider',
</code></pre>

<p>In the <code>$aliases</code> array add the following facade for this package.</p>

<pre><code>'Cart' =&gt; 'Cartalyst\Cart\Laravel\Facades\Cart',
</code></pre>

<h3>Configuration</h3>

<p>After installing, you can publish the package configuration file into your application by running the following command on your terminal:</p>

<pre><code>php artisan config:publish cartalyst/cart
</code></pre>

<p>This will publish the config file to <code>app/config/packages/cartalyst/cart/config.php</code> where you can modify the package configuration.</p>

<h2>Native</h2>

<p>Integrating the package outside of a framework is incredible easy, just follow the example below.</p>

<pre class="prettyprint lang-php"><code>// Include the composer autoload file
require_once 'vendor/autoload.php';

// Import the necessary classes
use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\NativeSession;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\Store;

// Require the cart config file
$config = require_once 'vendor/cartalyst/cart/src/config/config.php';

// Instantiate a new Session storage
$fileSessionHandler = new FileSessionHandler(new Filesystem(), __DIR__.'/storage/sessions');

$store = new Store('your_app_session_name', $fileSessionHandler);

$session = new NativeSession($store, $config['session_key'], $config['instance']);

// Instantiate the Cart and set the necessary configuration
$cart = new Cart('cart', $session, new Dispatcher);

$cart-&gt;setRequiredIndexes($config['requiredIndexes']);

// Get all the items from the cart
$items = $cart-&gt;items();
</code></pre>

<blockquote>
<p><strong>Note 1:</strong> Please make sure that the <code>storage/sessions</code> folder exists and has write access by the web server. This can be changed to another folder if required.</p>

<p><strong>Note 2:</strong> To setup garbage collection, call the <code>gc()</code> method on the FileSessionHandler <code>$fileSessionHandler-&gt;gc($lifetime);</code>, You can also setup a function that randomizes calls to this function rather than calling it on every request.</p>
</blockquote><h1>Usage</h1>

<p>In this section we'll show how you can manage your shopping cart.</p>

<h2>Add Item</h2>

<p>Having the ability to add items to the shopping cart is crucial and we've made it incredible simple to do it.</p>

<p>You can pass a simple or a multidimensional array and to help you get started, we have listed below all the default <code>indexes</code> that you can pass when adding or updating a cart item.</p>

<p><a id="indexes"></a></p>

<table>
<thead>
<tr>
<th>Key</th>
<th>Required</th>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>id</td>
<td>true</td>
<td>mixed</td>
<td>The item unique identifier, can be a numeric id, an sku, etc..</td>
</tr>
<tr>
<td>name</td>
<td>true</td>
<td>string</td>
<td>The item name.</td>
</tr>
<tr>
<td>price</td>
<td>true</td>
<td>float</td>
<td>The item price.</td>
</tr>
<tr>
<td>quantity</td>
<td>true</td>
<td>int</td>
<td>The quantity, needs to be an integer and can't be a negative value.</td>
</tr>
<tr>
<td>attributes</td>
<td>false</td>
<td>array</td>
<td>The item <a href="#attributes">attributes</a> like size, color, fabric, etc..</td>
</tr>
<tr>
<td>weight</td>
<td>false</td>
<td>float</td>
<td>The item weight.</td>
</tr>
</tbody>
</table>

<blockquote>
<p><strong>Note:</strong> You can pass custom <code>key</code>/<code>value</code> pairs into the array when adding or updating an item, please check the examples below.</p>
</blockquote>

<h3>Cart::add()</h3>

<table>
<thead>
<tr>
<th>Param</th>
<th>Required</th>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>$item</td>
<td>true</td>
<td>array</td>
<td>A single or multidimensional array that respects the list of <a href="#indexes">indexes</a> above.</td>
</tr>
</tbody>
</table>

<h4>Add a single item</h4>

<pre class="prettyprint lang-php"><code>Cart::add([
    'id'       =&gt; 'tshirt',
    'name'     =&gt; 'T-Shirt',
    'quantity' =&gt; 1,
    'price'    =&gt; 12.50,
]);
</code></pre>

<h4>Add a single item with a custom <code>index</code></h4>

<pre class="prettyprint lang-php"><code>Cart::add([
    'id'       =&gt; 'tshirt',
    'name'     =&gt; 'T-Shirt',
    'quantity' =&gt; 1,
    'price'    =&gt; 12.50,
    'sku'      =&gt; 'tshirt-custom',
]);
</code></pre>

<h4>Add a single item with <code>attributes</code> and a custom <code>index</code></h4>

<pre class="prettyprint lang-php"><code>Cart::add([
    'id'         =&gt; 'tshirt',
    'name'       =&gt; 'T-Shirt',
    'quantity'   =&gt; 1,
    'price'      =&gt; 12.50,
    'sku'        =&gt; 'tshirt-red-large',
    'attributes' =&gt; [

        'color' =&gt; [
            'label' =&gt; 'Red',
            'value' =&gt; 'red',
        ],

        'size' =&gt; [
            'label' =&gt; 'Large',
            'value' =&gt; 'l',
        ],

    ],
]);
</code></pre>

<h4>Adding multiple items</h4>

<pre class="prettyprint lang-php"><code>Cart::add([

    [
        'id'         =&gt; 'tshirt',
        'name'       =&gt; 'T-Shirt',
        'quantity'   =&gt; 1,
        'price'      =&gt; 12.50,
        'sku'        =&gt; 'tshirt-red-large',
        'attributes' =&gt; [

            'color' =&gt; [
                'label' =&gt; 'Red',
                'value' =&gt; 'red',
            ],

            'size' =&gt; [
                'label' =&gt; 'Large',
                'value' =&gt; 'l',
            ],

        ],
    ],

    [
        'id'       =&gt; 'sweatshirt',
        'name'     =&gt; 'Sweatshirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 98.32,
    ],

]);
</code></pre>

<h2>Update Item</h2>

<p>Updating items is as simple as adding them.</p>

<h3>Cart::update()</h3>

<table>
<thead>
<tr>
<th>Param</th>
<th>Required</th>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>$rowId</td>
<td>true</td>
<td>string</td>
<td>The item row id.</td>
</tr>
<tr>
<td>$data</td>
<td>true</td>
<td>mixed</td>
<td>This can be either an array or an integer, if an integer, it'll update the item quantity.</td>
</tr>
</tbody>
</table>

<blockquote>
<p><strong>Note:</strong> If the <code>$data</code> is an array, it doesn't require you to pass all the <code>indexes</code>, just the ones you wish to update, like <code>name</code>, <code>price</code>, <code>quantity</code>, <code>attributes</code>, etc..</p>
</blockquote>

<h4>Update an item quantity</h4>

<pre class="prettyprint lang-php"><code>Cart::update('c14c437bc9ae7d35a7c18ee151c6acc0', 2);
</code></pre>

<h4>Update a single item</h4>

<pre class="prettyprint lang-php"><code>Cart::update('c14c437bc9ae7d35a7c18ee151c6acc0', [
    'quantity' =&gt; 1,
    'price'    =&gt; 12.50,
]);
</code></pre>

<h4>Update multiple items</h4>

<pre class="prettyprint lang-php"><code>Cart::update([

    'c14c437bc9ae7d35a7c18ee151c6acc0' =&gt; [
        'id'       =&gt; 'tshirt',
        'name'     =&gt; 'T-Shirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 12.50,
    ],

    '63e2d7033fe95b9134a5737503d10ba5' =&gt; [
        'id'       =&gt; 'sweatshirt',
        'name'     =&gt; 'Sweatshirt',
        'quantity' =&gt; 2,
        'price'    =&gt; 98.32,
    ],

]);
</code></pre>

<h2>Remove Item</h2>

<p>Removing items from the cart is easy, you can remove one item at a time or multiple by providing an array containing the row ids that you wish to remove.</p>

<h3>Cart::remove()</h3>

<table>
<thead>
<tr>
<th>Param</th>
<th>Required</th>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>$items</td>
<td>true</td>
<td>mixed</td>
<td>This can be either a string or an array containing item row ids.</td>
</tr>
</tbody>
</table>

<h4>Remove a single item</h4>

<pre class="prettyprint lang-php"><code>Cart::remove('c14c437bc9ae7d35a7c18ee151c6acc0');
</code></pre>

<h4>Remove multiple items</h4>

<pre class="prettyprint lang-php"><code>Cart::remove([
    'c14c437bc9ae7d35a7c18ee151c6acc0',
    '63e2d7033fe95b9134a5737503d10ba5',
]);
</code></pre>

<h2>Items</h2>

<p>Need to show the items that are inside your shopping cart? We've you covered!</p>

<p>You can list all the items or grab individual items using their row ids.</p>

<h4>Get all the items</h4>

<pre class="prettyprint lang-php"><code>$items = Cart::items();

foreach ($items as $item)
{
    echo $item-&gt;price();
}
</code></pre>

<h4>Check if an item exists</h4>

<p>This method is most useful when deleting cart items, you can check if the item still exists on the cart before deleting it.</p>

<pre class="prettyprint lang-php"><code>if (Cart::exists('c14c437bc9ae7d35a7c18ee151c6acc0'))
{
    Cart::remove('c14c437bc9ae7d35a7c18ee151c6acc0');
}
</code></pre>

<h4>Get a single item</h4>

<pre class="prettyprint lang-php"><code>$item = Cart::item('c14c437bc9ae7d35a7c18ee151c6acc0');
</code></pre>

<h4>Get the item price</h4>

<pre class="prettyprint lang-php"><code>$item-&gt;price();
</code></pre>

<h4>Get the item quantity</h4>

<pre class="prettyprint lang-php"><code>$item-&gt;quantity();
</code></pre>

<h4>Get the item subtotal</h4>

<pre class="prettyprint lang-php"><code>$item-&gt;subtotal();
</code></pre>

<h4>Get the item weight</h4>

<pre class="prettyprint lang-php"><code>$item-&gt;weight();
</code></pre>

<h4>Get the item attributes</h4>

<pre class="prettyprint lang-php"><code>$item-&gt;attributes();
</code></pre>

<h2>Other Methods</h2>

<p>In this section we're covering all the other methods that didn't fit in on the previous sections.</p>

<h3>Cart::total()</h3>

<p>Returns the cart total.</p>

<pre class="prettyprint lang-php"><code>echo Cart::total();
</code></pre>

<h3>Cart::subtotal()</h3>

<p>Returns the cart subtotal.</p>

<pre class="prettyprint lang-php"><code>echo Cart::subtotal();
</code></pre>

<h3>Cart::quantity()</h3>

<p>Returns the total number of items that are in the cart.</p>

<pre class="prettyprint lang-php"><code>echo Cart::quantity();
</code></pre>

<h3>Cart::weight()</h3>

<p>Returns the total cart weight.</p>

<pre class="prettyprint lang-php"><code>echo Cart::weight();
</code></pre>

<h3>Cart::itemsSubtotal()</h3>

<p>Get the subtotal of the items in the Cart</p>

<pre class="prettyprint lang-php"><code>echo Cart::itemsSubtotal();
</code></pre>

<h3>Cart::clear()</h3>

<p>Empty the Cart</p>

<pre class="prettyprint lang-php"><code>Cart::clear();
</code></pre>

<h3>Cart::sync()</h3>

<p>This method is very useful when you want to synchronize a shopping cart that is stored on the database for example.</p>

<p>In this quick example, we're using a static array.</p>

<pre class="prettyprint lang-php"><code>$items = [

    [
        'id'       =&gt; 'tshirt',
        'name'     =&gt; 'T-Shirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 12.50,
    ],

    [
        'id'       =&gt; 'sweatshirt',
        'name'     =&gt; 'Sweatshirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 98.32,
    ],

];

$collection = new Collection($items);

Cart::sync($collection);
</code></pre><h2>Search</h2>

<p>If you ever need to search the shopping cart, we've, once again, you covered!</p>

<p>You can use one or multiple properties to search for items in the cart</p>

<h3>Cart::find()</h3>

<table>
<thead>
<tr>
<th>Param</th>
<th>Required</th>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>$data</td>
<td>true</td>
<td>array</td>
<td>Array of properties you want to search.</td>
</tr>
</tbody>
</table>

<h4>Example 1</h4>

<p>Search for an item that has the id <code>foobar</code></p>

<pre class="prettyprint lang-php"><code>Cart::find([

    'id' =&gt; 'foobar',

]);
</code></pre>

<h4>Example 2</h4>

<p>Search for an item thas has the name <code>Foo Bar</code> and the price <code>5</code></p>

<pre class="prettyprint lang-php"><code>Cart::find([

    'name'  =&gt; 'Foo Bar',
    'price' =&gt; 5,

]);
</code></pre>

<h4>Example 3</h4>

<p>Search for items with the following attributes</p>

<pre class="prettyprint lang-php"><code>Cart::find([

    'attributes' =&gt; [

        'size' =&gt; [
            'price' =&gt; 5,
        ],

    ],

]);
</code></pre><h2>Attributes</h2>

<p>Each item can have different attributes like size, color and you can even add a price to each attribute that will reflect on the final item price.</p>

<table>
<thead>
<tr>
<th>Key</th>
<th>Required</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>label</td>
<td>true</td>
<td>The name that is displayed to the end user.</td>
</tr>
<tr>
<td>value</td>
<td>true</td>
<td>The attribute value.</td>
</tr>
<tr>
<td>price</td>
<td>false</td>
<td>The attribute price.</td>
</tr>
<tr>
<td>weight</td>
<td>false</td>
<td>The attribute weight.</td>
</tr>
</tbody>
</table>

<blockquote>
<p><strong>Note:</strong> You can pass custom <code>key</code>/<code>value</code> pairs into the array, please check the examples below.</p>
</blockquote>

<pre class="prettyprint lang-php"><code>Cart::add([

    'id'         =&gt; 'tshirt',
    'name'       =&gt; 'T-Shirt',
    'quantity'   =&gt; 1,
    'price'      =&gt; 12.50,
    'attributes' =&gt; [

        'size' =&gt; [
            'label' =&gt; 'Large',
            'value' =&gt; 'l',
            'price' =&gt; 5,
        ],

        'color' =&gt; [
            'label' =&gt; 'Red',
            'value' =&gt; 'red',
        ],

    ],

]);
</code></pre><h2>Collections</h2>

<p>Collections are merely a wrapper for an array of objects, but offers a bunch of other useful methods to help you pluck items out of the array.</p>

<p>Below we'll reference a few of the many useful methods available within the Laravel <a href="https://github.com/laravel/framework/blob/master/src/Illuminate/Support/Collection.php"><code>Collection</code></a> class.</p>

<h4>first()</h4>

<p>The <code>first()</code> method can be used to retrieve the first element in the collection.<br>
This will be the first element contained within the collection internal array.</p>

<pre><code>$item = Cart::items()-&gt;first();
</code></pre>

<h4>last()</h4>

<p>The <code>last()</code> method does the opposite of the <code>first()</code> method and returns the last element contained within the collection internal array.</p>

<pre><code>$item = Cart::items()-&gt;last();
</code></pre>

<h4>isEmpty()</h4>

<p>The <code>isEmpty()</code> method can be used to check whether or not the collection has elements within it. It accepts no value and returns a boolean.</p>

<pre><code>if ( ! Cart::items()-&gt;isEmpty())
{
    echo 'We have items on our cart :)';
}
else
{
    echo 'Cart is empty :(';
}
</code></pre>

<h4>count()</h4>

<p>The <code>count()</code> method counts the number of items in the collection.</p>

<pre><code>$total = Cart::items()-&gt;count();
</code></pre><h2>Instances</h2>

<p>Cart supports multiple cart instances, so that you can have as many shopping cart instances on the same page as you want without any conflicts.</p>

<p>You have two ways of accomplishing this, one is by creating a service provider dedicated to your wishlist to register all your other cart instances, the second method, which is easier, is to bind the new cart instances directly into the IoC.</p>

<h3>Example</h3>

<h4>IoC Binding</h4>

<pre class="prettyprint lang-php"><code>use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\Sessions\IlluminateSession;

$app = app();

$app['wishlist'] = $app-&gt;share(function($app)
{
    $config = $app['config']-&gt;get('cartalyst/cart::config');

    $storage = new IlluminateSession($app['session.store'], $config['session_key'], 'wishlist');

    return new Cart('wishlist', $storage, $app['events']);
});
</code></pre>

<h4>Create your Service Provider</h4>

<p><code>app/services/WishlistServiceProvider.php</code></p>

<pre class="prettyprint lang-php"><code>use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\Sessions\IlluminateSession;
use Illuminate\Support\ServiceProvider;

class WishlistServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this-&gt;registerSession();

        $this-&gt;registerCart();
    }

    /**
     * Register the session driver used by the Wishlist.
     *
     * @return void
     */
    protected function registerSession()
    {
        $this-&gt;app['wishlist.storage'] = $this-&gt;app-&gt;share(function($app)
        {
            $config = $app['config']-&gt;get('cartalyst/cart::config');

            return new IlluminateSession($app['session.store'], $config['session_key'], 'wishlist');
        });
    }

    /**
     * Register the Wishlist.
     *
     * @return void
     */
    protected function registerCart()
    {
        $this-&gt;app['wishlist'] = $this-&gt;app-&gt;share(function($app)
        {
            return new Cart('wishlist', $app['wishlist.storage'], $app['events']);
        });
    }

}
</code></pre>

<h4>Create your Facade</h4>

<p><code>app/facades/Wishlist.php</code></p>

<pre class="prettyprint lang-php"><code>use Illuminate\Support\Facades\Facade;

class Wishlist extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'wishlist';
    }

}
</code></pre>

<h4>Register your Service Provider and Facade</h4>

<p>Open your Laravel config file <code>app/config/app.php</code> and add the following lines.</p>

<p>In the <code>$providers</code> array add the following service provider for this package.</p>

<pre><code>'Path\To\Your\WishlistServiceProvider',
</code></pre>

<p>In the <code>$aliases</code> array add the following facade for this package.</p>

<pre><code>'Wishlist' =&gt; 'Path\To\Your\Wishlist',
</code></pre>

<h4>Usage</h4>

<p>Usage is identical to the cart.</p>

<pre class="prettyprint lang-php"><code>Wishlist::add([
        'id'       =&gt; 'tshirt',
        'name'     =&gt; 'T-Shirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 12.50,
]);
</code></pre><h2>Conditions</h2>

<p>The Cart package utilizes the Cartalyst Conditions package to manage item and cart based conditions.</p>

<p>The Cart has a default set of three condition types: <code>discount</code>, <code>tax</code> and <code>other</code> conditions.</p>

<h3>Applying Conditions</h3>

<h4>Cart Conditions</h4>

<p>You can pass an array containing one or multiple conditions to the cart.</p>

<p><strong>Apply a single condition</strong></p>

<pre class="prettyprint lang-php"><code>$condition = new Condition([
    'name'   =&gt; 'VAT (12.5%)',
    'type'   =&gt; 'tax',
    'target' =&gt; 'subtotal',
]);

$condition-&gt;setActions([

    [
        'value' =&gt; '12.5%',
    ],

]);

Cart::condition($condition);
</code></pre>

<p><strong>Apply multiple conditions</strong></p>

<pre class="prettyprint lang-php"><code>$conditionTax = new Condition([
    'name'   =&gt; 'VAT (12.5%)',
    'type'   =&gt; 'tax',
    'target' =&gt; 'subtotal',
]);

$conditionTax-&gt;setActions([

    [
        'value' =&gt; '12.5%',
    ],

]);

$conditionDiscount = new Condition([
    'name'   =&gt; 'Discount (2.5%)',
    'type'   =&gt; 'discount',
    'target' =&gt; 'subtotal',
]);

$conditionDiscount-&gt;setActions([

    [
        'value' =&gt; '-2.5%',
    ],

]);

Cart::condition([$conditionTax, $conditionDiscount]);
</code></pre>

<h4>Item Conditions</h4>

<p>You can add one or more (array) conditions to an item that will be assigned automatically when adding or updating items on the cart.</p>

<pre class="prettyprint lang-php"><code>$condition = new Condition([
    'name'   =&gt; 'VAT (12.5%)',
    'type'   =&gt; 'tax',
    'target' =&gt; 'subtotal',
]);

Cart::add([
    'id'         =&gt; 'tshirt',
    'name'       =&gt; 'T-Shirt',
    'quantity'   =&gt; 1,
    'price'      =&gt; 12.50,
    'conditions' =&gt; $condition,
]);
</code></pre>

<h3>Removing Conditions</h3>

<p>There will be times you'll need to remove conditions, in this section we'll cover how to remove specific conditions or all the conditions at once.</p>

<h3>Cart::removeConditionByName()</h3>

<p>Removes a condition by its name.</p>

<table>
<thead>
<tr>
<th>Param</th>
<th>Required</th>
<th>Type</th>
<th>Default</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>$name</td>
<td>true</td>
<td>string</td>
<td>null</td>
<td>The condition name.</td>
</tr>
<tr>
<td>$includeItems</td>
<td>false</td>
<td>boolean</td>
<td>true</td>
<td>Flag to either remove the condition from the items or not.</td>
</tr>
</tbody>
</table>

<pre class="prettyprint lang-php"><code>Cart::removeConditionByName('Tax 10%');
</code></pre>

<h3>Cart::removeConditionByType()</h3>

<p>Removes a condition by its type.</p>

<table>
<thead>
<tr>
<th>Param</th>
<th>Required</th>
<th>Type</th>
<th>Default</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>$type</td>
<td>true</td>
<td>string</td>
<td>null</td>
<td>The condition type.</td>
</tr>
<tr>
<td>$includeItems</td>
<td>false</td>
<td>boolean</td>
<td>true</td>
<td>Flag to either remove the condition from the items or not.</td>
</tr>
</tbody>
</table>

<pre class="prettyprint lang-php"><code>Cart::removeConditionByType('tax');
</code></pre>

<h3>Cart::removeConditions()</h3>

<p>Removes all conditions with the given identifier.</p>

<table>
<thead>
<tr>
<th>Param</th>
<th>Required</th>
<th>Type</th>
<th>Default</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>$id</td>
<td>false</td>
<td>mixed</td>
<td>null</td>
<td>The condition identifier.</td>
</tr>
<tr>
<td>$includeItems</td>
<td>false</td>
<td>boolean</td>
<td>true</td>
<td>Flag to either remove the condition from the items or not.</td>
</tr>
<tr>
<td>$target</td>
<td>false</td>
<td>string</td>
<td>type</td>
<td>The target that the condition was applied on.</td>
</tr>
</tbody>
</table>

<p><strong>Remove the condition with the type <code>tax</code>.</strong></p>

<pre class="prettyprint lang-php"><code>Cart::removeConditions('tax');
</code></pre>

<p><strong>Remove all the applied conditions</strong></p>

<pre class="prettyprint lang-php"><code>Cart::removeConditions();
</code></pre>

<h3>Conditions Types</h3>

<p>Conditions types are defined by the type property on conditions.</p>

<p>Cart handles discount, other and tax types by default.</p>

<blockquote>
<p><strong>Note:</strong> If you need to define custom condition types, make sure you set the conditions order using <code>Cart::setConditionsOrder($types)</code> by passing it an array of types that should be handled by the cart, otherwise only default condition types will be applied.</p>
</blockquote>

<h4>Tax</h4>

<p>Tax conditions must have the type set to tax</p>

<pre class="prettyprint lang-php"><code>$condition = new Condition([
    'name'   =&gt; 'VAT (12.5%)',
    'type'   =&gt; 'tax',
    'target' =&gt; 'subtotal',
]);

$condition-&gt;setActions([

    [
        'value' =&gt; '12.5%',
    ],

]);
</code></pre>

<h4>Discount</h4>

<p>Discount conditions must have the type set to discount</p>

<pre class="prettyprint lang-php"><code>$condition = new Condition([
    'name'   =&gt; 'Discount (5%)',
    'type'   =&gt; 'discount',
    'target' =&gt; 'subtotal',
]);

$condition-&gt;setActions([

    [
        'value' =&gt; '-5%',
    ],

]);
</code></pre>

<p>The condition above will apply a 5% discount.</p>

<h4>Other</h4>

<p>Other conditions must have the type set to other</p>

<p>The condition below will add 5 to the subtotal after applying discounts (if any)<br>
assuming conditions order are set to their default order.</p>

<pre class="prettyprint lang-php"><code>$condition = new Condition([
    'name'   =&gt; 'Other (5%)',
    'type'   =&gt; 'other',
    'target' =&gt; 'subtotal',
]);

$condition-&gt;setActions([

    [
        'value' =&gt; '5',
    ],

]);
</code></pre>

<h3>Inclusive Conditions</h3>

<p>Inclusive conditions are not added to the total but allow you to reverse<br>
calculate taxes that are already included in your price.</p>

<p>This condition will be reverse calculated and will show up on total<br>
conditions methods, but it will not be added to the cart total.</p>

<pre class="prettyprint lang-php"><code>$condition = new Condition([
    'name'   =&gt; 'Tax (5%)',
    'type'   =&gt; 'tax',
    'target' =&gt; 'subtotal',
]);

$condition-&gt;setActions([

    [
        'value'     =&gt; '5%',
        'inclusive' =&gt; true,
    ],

]);
</code></pre>

<h3>Conditions Order</h3>

<h4>Default Order</h4>

<ul>
<li>discount</li>
<li>other</li>
<li>tax</li>
</ul>

<h4>Get Cart Conditions Order</h4>

<pre class="prettyprint lang-php"><code>$order = Cart::getConditionsOrder();
</code></pre>

<h4>Set Cart Conditions Order</h4>

<pre class="prettyprint lang-php"><code>Cart::setConditionsOrder([
    'discount',
    'other',
    'tax',
    'shipping',
]);
</code></pre>

<h4>Set Items Conditions Order</h4>

<pre class="prettyprint lang-php"><code>Cart::setItemsConditionsOrder([
    'discount',
    'other',
    'tax',
    'shipping',
]);
</code></pre>

<blockquote>
<p><strong>Note:</strong> Make sure you set Item conditions before adding or updating.</p>
</blockquote>

<h6>Condition targets</h6>

<p>You can apply item conditions either on the item price or subtotal.</p>

<ul>
<li>price</li>
<li>subtotal</li>
</ul>

<blockquote>
<p><strong>Note 1:</strong> Item conditions are always applied prior to Cart conditions.</p>

<p><strong>Note 2:</strong> Price-based item conditions are always applied prior to subtotal-based conditions.</p>
</blockquote>

<h3>Conditions Methods</h3>

<p>Apply a condition.</p>

<pre class="prettyprint lang-php"><code>Cart::condition(Cartalyst\Conditions\Condition $condition);
</code></pre>

<p>Return all applied conditions.</p>

<pre class="prettyprint lang-php"><code>Cart::conditions($type|null, bool $includeItems);
</code></pre>

<p>Set the order in which conditions are applied.</p>

<pre class="prettyprint lang-php"><code>Cart::setConditionsOrder([
    'discount',
    'other',
    'tax',
]);
</code></pre>

<p>Set the order in which conditions are applied on items.</p>

<pre class="prettyprint lang-php"><code>Cart::setItemsConditionsOrder([
    'discount',
    'tax',
    'shipping',
]);
</code></pre>

<p>Return all conditions totals grouped by type.</p>

<pre><code>Cart::conditionsTotal($type);  // Returns an array of results of the type passed including items conditions

Cart::conditionsTotal($type, false);  // Returns an array of results of the type passed excluding items conditions

Cart::conditionsTotal();  // Returns an array of results of all applied conditions including items conditions

Cart::conditionsTotal(null, false);  // Returns an array of results of all applied conditions excluding items conditions
</code></pre>

<p>Return the sum of all or a specific type of conditions.</p>

<pre><code>Cart::conditionsTotalSum($type); // Returns the sum of a the type passed

Cart::conditionsTotalSum(); // Returns the sum of all conditions
</code></pre>

<p>Return all conditions applied only to items.</p>

<pre><code>Cart::itemsConditions();
</code></pre>

<p>Return all or a specific type of items conditions sum grouped by type.</p>

<pre><code>Cart::itemsConditionsTotal($type); // Returns an array of results of the type passed

Cart::itemsConditionsTotal(); // Returns an array of results of all applied conditions
</code></pre>

<p>Return the sum of all or a specific type of items conditions.</p>

<pre><code>Cart::itemsConditionsTotalSum($type); // Returns the sum of the type passed

Cart::itemsConditionsTotalSum(); // Returns the sum of all conditions
</code></pre><h2>Events</h2>

<p>On this section we have a list of all the events fired by the cart that you can listen for.</p>

<table>
<thead>
<tr>
<th>Event</th>
<th>Parameters</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>cartalyst.cart.added</td>
<td>$item, $cart</td>
<td>Event fired when an item is added to the cart.</td>
</tr>
<tr>
<td>cartalyst.cart.removed</td>
<td>$item, $cart</td>
<td>Event fired when an item is removed from the cart.</td>
</tr>
<tr>
<td>cartalyst.cart.update</td>
<td>$item, $cart</td>
<td>Event fired when an item is updated.</td>
</tr>
<tr>
<td>cartalyst.cart.cleared</td>
<td>$cart</td>
<td>Event fired when the cart is cleared.</td>
</tr>
</tbody>
</table>

<h3>Examples</h3>

<p>Whenever an item is added to the shopping cart.</p>

<pre class="prettyprint lang-php"><code>Event::listen('cartalyst.cart.added', function($item, $cart)
{
    // Apply your own logic here
});
</code></pre>

<p>Whenever an item is removed from the shopping cart.</p>

<pre class="prettyprint lang-php"><code>Event::listen('cartalyst.cart.removed', function($item, $cart)
{
    // Apply your own logic here
});
</code></pre>

<p>Whenever an item is updated on the shopping cart.</p>

<pre class="prettyprint lang-php"><code>Event::listen('cartalyst.cart.updated', function($item, $cart)
{
    // Apply your own logic here
});
</code></pre>

<p>Whenever the shopping cart is cleared.</p>

<pre class="prettyprint lang-php"><code>Event::listen('cartalyst.cart.cleared', function($cart)
{
    // Apply your own logic here
});
</code></pre><h2>Exceptions</h2>

<p>On this section we provide a list of all the exceptions that are thrown by the cart.</p>

<p>The exceptions are thrown in the <code>Cartalyst\Cart\Exceptions</code> namespace.</p>

<table>
<thead>
<tr>
<th>Exception</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>CartMissingRequiredIndexException</td>
<td>This exception will be thrown whenever a required index is not provided.</td>
</tr>
<tr>
<td>CartInvalidQuantityException</td>
<td>This exception will be thrown when the provided quantity is invalid.</td>
</tr>
<tr>
<td>CartInvalidPriceException</td>
<td>This exception will be thrown when the provided price is invalid.</td>
</tr>
<tr>
<td>CartInvalidAttributesException</td>
<td>This exception will be thrown whenever the provided attributes are invalid or malformed.</td>
</tr>
<tr>
<td>CartItemNotFoundException</td>
<td>This exception will be thrown whenever you request an item that does not exist.</td>
</tr>
</tbody>
</table>

<h3>Examples</h3>

<p>Catch the exception when adding an item into the cart with a missing required index.</p>

<pre class="prettyprint lang-php"><code>try
{
    # We're not passing the price
    Cart::add([
        'id'       =&gt; 'tshirt',
        'name'     =&gt; 'T-Shirt',
        'quantity' =&gt; 1,
    ]);
}
catch (Cartalyst\Cart\Exceptions\CartMissingRequiredIndexException $e)
{
    # Grabbing the missing index
    $missingIndex = $e-&gt;getMessage();

    // Apply your own logic here
}
</code></pre>

<p>Catch the exception when adding an item with an invalid quantity value.</p>

<pre class="prettyprint lang-php"><code>try
{
    Cart::add([
        'id'       =&gt; 'tshirt',
        'name'     =&gt; 'T-Shirt',
        'quantity' =&gt; -1,
        'price'    =&gt; 12.50,
    ]);
}
catch (Cartalyst\Cart\Exceptions\CartInvalidQuantityException $e)
{
    // Apply your own logic here
}
</code></pre>

<p>Catch the exception when adding an item with an invalid price value.</p>

<pre class="prettyprint lang-php"><code>try
{
    Cart::add([
        'id'       =&gt; 'tshirt',
        'name'     =&gt; 'T-Shirt',
        'quantity' =&gt; 1,
        'price'    =&gt; 'abc',
    ]);
}
catch (Cartalyst\Cart\Exceptions\CartInvalidPriceException $e)
{
    // Apply your own logic here
}
</code></pre>

<p>Catch the exception when adding an item that contains invalid attributes.</p>

<pre class="prettyprint lang-php"><code>try
{
    Cart::add([
        'id'         =&gt; 'tshirt',
        'name'       =&gt; 'T-Shirt',
        'quantity'   =&gt; 1,
        'price'      =&gt; 12.50,
        'attributes' =&gt; 'abc',
    ]);
}
catch (Cartalyst\Cart\Exceptions\CartInvalidAttributesException $e)
{
    // Apply your own logic here
}
</code></pre>

<p>Catch the exception when trying to update an item that doesn't exist.</p>

<pre class="prettyprint lang-php"><code>try
{
    Cart::update('abc', [
        'price' =&gt; 20.00,
    ]);
}
catch (Cartalyst\Cart\Exceptions\CartItemNotFoundException $e)
{
    // Apply your own logic here
}
</code></pre>