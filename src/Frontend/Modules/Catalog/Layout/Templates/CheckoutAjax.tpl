{*
  variables that are available:
  - {$productsInShoppingCart}: contains data about the products
  - {$totalPrice}: total price of products
  - {$personalDataUrl}: url to personal data page
  - {$catalogUrl}: url to catalog page
*}

{option:productsInShoppingCart}
    {* classnames are important for javascript actions *}
    <div class="productsInShoppingCart">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>{$lblProduct|ucfirst}</th>
                <th>{$lblAmount|ucfirst}</th>
                <th>{$lblPrice|ucfirst}</th>
                <th>{$lblTotal|ucfirst}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            {iteration:productsInShoppingCart}
                <tr>
                    <td><a href="{$productsInShoppingCart.full_url}">{$productsInShoppingCart.title}</a></td>
                    <td>
                        <div class="editProductAmountInCheckout">
                            <input type="text" size="3" class="form-control" name="amountOfProducts" id="inputAmountOfProducts-{$productsInShoppingCart.product_id}" value="{$productsInShoppingCart.amount}">
                            <p><a href="#" id="{$productsInShoppingCart.product_id}">{$lblRecalculatePrice|ucfirst}</a>
                            </p>
                        </div>
                    </td>
                    <td>{$productsInShoppingCart.price|formatcurrency}</td>
                    <td>{$productsInShoppingCart.subtotal_price|formatcurrency}</td>
                    <td>
                        <div class="removeProductFromShoppingCart">
                            <a href="#" id="{$productsInShoppingCart.product_id}">{$lblDelete|ucfirst}</a></div>
                    </td>
                </tr>
            {/iteration:productsInShoppingCart}
            <tfoot>

            <tr>
                <td></td>
                <td></td>
                <th>{$lblTotal|ucfirst}</th>
                <th>{$totalPrice|formatcurrency}</th>
                <td></td>
            </tr>
            </tfoot>
            </tbody>
        </table>
    </div>
    <div class="clearfix">&nbsp;</div>
{/option:productsInShoppingCart}

{option:!productsInShoppingCart}
    <p>{$msgNoProductsInShoppingCart|ucfirst}</p>
{/option:!productsInShoppingCart}

{option:productsInShoppingCart}
    <p><a href="{$personalDataUrl}" class="btn btn-success pull-right">{$lblGoToPersonalData|ucfirst}</a></p>
{/option:productsInShoppingCart}

<p><a href="{$catalogUrl}">{$lblContinueShopping|ucfirst}</a></p>
