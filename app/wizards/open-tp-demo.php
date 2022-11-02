
<div class="screen-wrapper">
    <form id="open-tp-demo">
        <div class="row">
            <div class="col-sm-12 mb-3">
                <label for="inputPlatform">Platform</label>
                <select class="form-control" name="platform" id="platform" required>
                    <option value="">Please Select a Platform</option>
                    <option value="2" selected>MT5</option>
                    <option value="1">MT4</option>
                </select>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="inputType">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Please Select a Type</option>
                    <option value="2" disabled>Real</option>
                    <option value="1" selected>Demo</option>
                </select>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="inputComment">Group</label>
                <select class="form-control" name="group" id="group" required>
                    <option value="">Please Select Type &amp; Platform</option>
                    <!--<option value='demoKUSTDFIXUSD'>demoKUSTDFIXUSD</option>-->
                </select>
            </div>
            <div class="col-sm-12 mb-3">
                <label for="inputAmount">Amount</label>
                <input type="number" min="100" max="100000" class="form-control" id="amount" placeholder="Deposit Amount" name="amount" required>
            </div>
        </div>
        <div class="col-sm-12 mb-3 text-center">
            <button type="submit" class="btn col-12 btn-primary" >Open The Account</button>
        </div>
    </form>

</div>