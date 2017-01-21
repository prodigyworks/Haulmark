<style>
    .entryform .bubble {
        display: none;
    }
</style>
<table   cellpadding="0" cellspacing="4" class="entryformclass">
    <tbody>
    <tr valign="center">
        <td>Customer</td>
        <td colspan=2>
            <?php createCombo("customerid", "id", "name", "{$_SESSION['DB_PREFIX']}customer", "", true); ?>
        </td>
    </tr>
    <tr valign="center">
        <td>Account Code</td>
        <td colspan=2>
            <input type="text" id="accountcode" name="accountcode" size="9" readonly  />
        </td>
    </tr>
    <tr valign="center">
        <td>&nbsp;</td>
        <td>
            <div>Invoice Address</div>
            <textarea readonly id="invoiceaddress" name="invoiceaddress" cols="50" rows="6"></textarea>
        </td>
    </tr>
    <tr valign="center">
        <td>Revision</td>
        <td colspan=2>
            <input type="text" style="width:60px" id="revision" readonly name="revision">
            <input type="hidden" id="item_serial" name="item_serial" />
            <input type="hidden" id="deliveryaddress" name="deliveryaddress" />
        </td>
    </tr>
    <tr valign="center">
        <td>Invoice Date</td>
        <td colspan=2>
            <input type="text" class="datepicker" id="orderdate" name="orderdate">
        </td>
    </tr>
    <tr valign="center">
        <td>Your Order Number</td>
        <td colspan=2>
            <input type="text" style="width:260px" id="yourordernumber" name="yourordernumber">
        </td>
    </tr>
    <tr valign="center">
        <td>Exported</td>
        <td colspan=2>
        	<SELECT id="exported" name="exported">
        		<OPTION value="N">No</OPTION>
        		<OPTION value="Y">Yes</OPTION>
        	</SELECT>
        </td>
    </tr>
    <tr valign="center">
        <td>Taken By</td>
        <td colspan=2>
            <?php createUserCombo("takenbyid"); ?>
        </td>
    </tr>
    <tr valign="center">
        <td>Discount %</td>
        <td colspan=2>
            <input type="number" style="width:70px" id="discount" name="discount" onchange="total_onchange()" value="0.00">
        </td>
    </tr>
    <tr valign="center">
        <td>Total (Nett)</td>
        <td colspan=2>
            <input type="number" style="width:70px" id="total" name="total" readonly value="0.00">
        </td>
    </tr>
    <tr valign="center">
        <td>Total (Gross)</td>
        <td colspan=2>
            <input type="number" style="width:70px" id="totalgross" name="totalgross" readonly value="0.00">
        </td>
    </tr>
    </tbody>
</table>

<table>
    <tr>
        <td>
		   	<span class="wrapper">
		   		<a class='rgap2 link2' href="javascript:addInvoiceItem()">
                    <em>
                        <b>
                            <img src='images/add.png' /> Add
                        </b>
                    </em>
                </a>
		   	</span>
        </td>
    </tr>
</table>
<?php
function createHeader() {
    ?><tr><td width='13px'>Actions</td><td width='100px'>Job Number</td><td  width='450px'align='left'>Journey</td><td  width='100px' align='right'>Total (Nett)</td></tr><?php
}
?>
<div id="divtable">
    <table width="100%" class="grid list">
        <thead>
        <?php createHeader(); ?>
        </thead>
    </table>
</div>

<div id="invoiceitemdialog" class="modal">
    <table  cellpadding="0" cellspacing="4" class="entryformclass">
        <tbody>
        <tr valign="center">
            <td width='120px'>Job Number</td>
            <td>
                <?php createBookingCombo("item_productid", "WHERE statusid >= 7"); ?>
                <input type="hidden" id="item_productdesc" name="item_productdesc"  />
            </td>
        </tr>
        <tr valign="center">
            <td>Journey</td>
            <td>
                <input type="text" readonly id="item_description" name="item_description"  style="width:500px" />
            </td>
        </tr>
        <tr valign="center">
            <td>Quantity</td>
            <td>
                <input type="number" id="item_quantity" name="item_quantity" size=9 onchange="productid_onchange()" />
                <input type="hidden" id="item_id" name="item_id" />
            </td>
        </tr>
        <tr valign="center">
            <td>Unit Price</td>
            <td>
                <input type="number" id="item_unitprice" name="item_unitprice" size=9 onchange="qty_onchange()" />
            </td>
        </tr>
        <tr valign="center">
            <td>VAT Rate</td>
            <td>
                <input type="number" id="item_vatrate" name="item_vatrate" size=9 onchange="qty_onchange()" />
            </td>
        </tr>
        <tr valign="center">
            <td>VAT</td>
            <td>
                <input type="number" id="item_vat" name="item_vat" size=9 readonly />
            </td>
        </tr>
        <tr valign="center">
            <td>Line Total</td>
            <td>
                <input type="number" id="item_linetotal" name="item_linetotal" size=9 onchange="qty_onchange()" />
            </td>
        </tr>
        <tr valign="center">
            <td>Nominal Ledger Code</td>
            <td>
            	<?php createCombo("item_nominalledgercodeid", "id", "name", "{$_SESSION['DB_PREFIX']}nominalledgercode"); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>