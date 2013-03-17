<?php namespace Cartalyst\Cartify\Cart;

class InvalidDataException extends \UnexpectedValueException {}
class RequiredIndexException extends \UnexpectedValueException {}
class ItemNotFoundException extends \OutOfBoundsException {}
class InvalidItemIdException extends \UnexpectedValueException {}
class InvalidItemNameException extends \UnexpectedValueException {}
class InvalidItemQuantityException extends \UnexpectedValueException {}
class InvalidItemPriceException extends \UnexpectedValueException {}
