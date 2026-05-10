# TODO

## Cart timing fix (Phase 1)
- [x] Remove premature `session()->forget('cart')` from `app/Livewire/ShoppingCart.php`.
- [x] Remove premature `session()->forget('cart')` from `app/Http/Controllers/OrderController.php`.
- [x] Clear cart only after successful payment in `app/Http/Controllers/PaymentController.php::handleGatewayCallback()` using only `logger()` + `session()->flash()`.
- [ ] Verify UI/logic integration:
  - [ ] Livewire requests reach `/livewire/update` (confirm in browser devtools/network).
  - [ ] Session cart persists after `addToCart()` and remains during checkout redirection.
  - [ ] Order items are saved correctly in DB during `OrderService::createOrder()`.
  - [ ] Cart is cleared only after the payment success callback returns.

