
#Cart Change Log

###Minor | v1.1.0 | 2014-10-27

`ADDED` A new catalyst.cart.created event which is fired when a cart instance is initialized.

`REVISED` The conditions validation.

`REVISED` The Cart MetaData feature once again to be more flexible.

`REMOVED` The updateCart() method and it's corresponding calls, keeping the code more simplified.

`REVISED` Minor tweaks and improvements.

####Patch | v1.0.7 | 2014-10-19

`ADDED` Allow adding free items (price = 0.00) into the cart.

####Patch | v1.0.6 | 2014-10-11

`FIXED` A bug causing removed item conditions to be reapplied after updating the cart.


####Patch | v1.0.5 | 2014-10-05

`ADDED` Flag (bool) to the price(:withAttributes) method to return the item price + the item attributes total.

####Patch | v1.0.4 | 2014-09-23

`REVISED` Loosen requirements to allow the usage on Laravel 5.0.

####Patch | v1.0.3 | 2014-09-15

`REVISED` Tweak to check the condition result before applying the actions.

####Patch | v1.0.2 | 2014-09-05

`ADDED` A IoC Container alias for the Cart class.
`ADDED` The provides() method to the Service Provider.
`REVISED` Unit tests improved.

####Patch | v1.0.1 | 2014-07-14

`REVISED` Improved the setMetadata() method to allow old values to be merged when setting new values on an existing key.

##MAJOR | v1.0.0 | 2014-05-09

`ADDED` Cart::getIdentity() Returns the cart identity.

`ADDED` Cart::setIdentity($name) Sets the cart identity.

`ADDED` Cart::add($item) Adds a single item to the cart.

`ADDED` Cart::add($items) Adds multiple items to the cart.

`ADDED` Cart::remove($rowId) Removes an item from the cart.

`ADDED` Cart::remove([$rowId, $rowId]) Removes multiple items from the cart by passing an array.

`ADDED` Cart::update($rowId, $data) Updates a single item.

`ADDED` Cart::update($items) Updates multiple items.

`ADDED` Cart::update($rowId, $quantity) Updates an item quantity.

`ADDED` Cart::exists($rowId) Check if the given item exists.

`ADDED` Cart::item($rowId) Returns information of the given item.

`ADDED` Cart::items() Returns all items.

`ADDED` Cart::itemsSubtotal() Returns the subtotal of the items without conditions.

`ADDED` Cart::quantity() Returns the total # of items that are in the cart.

`ADDED` Cart::subtotal() Returns the subtotal of the cart.

`ADDED` Cart::total($type|null) Returns subtotal after applying upto a specific condition type or null to calculate total.

`ADDED` Cart::weight() Returns the total cart weight.

`ADDED` Cart::clear() Empty the cart.

`ADDED` Cart::sync(Collection $items) Synchronizes a collection of data with the cart.

`ADDED` Cart::find($data) Search for items that are in the cart.

`ADDED` Cart::condition(Cartalyst\Conditions\Condition $condition) Applies a condition on the cart.

`ADDED` Cart::conditions($type|null, bool $includeItems) Returns all the applied conditions.

`ADDED` Cart::getConditionsOrder() Returns the order in which the conditions are applied.

`ADDED` Cart::setConditionsOrder($array) Sets the order in which the conditions are applied.

`ADDED` Cart::getItemsConditionsOrder() Returns the order in which the conditions are applied on items.

`ADDED` Cart::setItemsConditionsOrder($array) Sets the order in which the conditions are applied on items.

`ADDED` Cart::conditionsTotal($type|null, bool $includeItems) Returns all conditions totals grouped by type.

`ADDED` Cart::conditionsTotalSum($type|null) Returns the sum of all or a specific type of conditions.

`ADDED` Cart::itemsConditions() Returns all conditions applied only to items.

`ADDED` Cart::itemsConditionsTotal($type|null) Returns all or a specific type of items conditions sum grouped by type.

`ADDED` Cart::itemsConditionsTotalSum($type|null) Returns the sum of all or a specific type of items conditions.

`ADDED` Cart::removeConditionByName($name, bool $includeItems) Removes an applied condition by name.

`ADDED` Cart::removeConditionByType($name, bool $includeItems) Removes an applied condition by type.

`ADDED` Cart::removeConditions($id, bool $includeItems) Removes all or a specific type of applied conditions.

`ADDED` Cart::setMetaData($array) Sets the meta data on the cart.

`ADDED` Cart::getMetaData($key|null) Returns all or a specific key of meta data.

`ADDED` Cart::removeMetaData($key|null) Removes all or a specific key of meta data.

`ADDED` Cart::getRequiredIndexes() Returns the required indexes.

`ADDED` Cart::setRequiredIndexes(array $indexes, bool $merge) Sets the required indexes.
