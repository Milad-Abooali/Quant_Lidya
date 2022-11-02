<section class="<?= $href ?>">

    <h6 class="text-center">Add New Broker</h6>
    <div>

        <form id="new-broker">
            <div class="row">
                <div class="col-md-6">
                    <table class="table-sm table-hover w-100">
                        <tbody>
                        <tr>
                            <td>Title</td>
                            <td><input class="form-control" name="title" required></td>

                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input class="form-control" name="email"  required></td>
                        </tr>
                        <tr>
                            <td>Web Site</td>
                            <td><input class="form-control" name="web_url" required></td>
                        </tr>
                        <tr>
                            <td>CRM</td>
                            <td><input class="form-control" name="crm_url" required></td>
                        </tr>
                        <tr>
                            <td>PHP Session Path</td>
                            <td><input class="form-control" name="session_path" required></td>
                        </tr>
                        <tr>
                            <td>TOS File Path</td>
                            <td><input class="form-control" name="terms_file" required></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table-sm table-hover w-100">
                        <tbody>
                        <tr>
                            <td>Defualt Language</td>
                            <td><input class="form-control" name="def_language" required></td>
                        </tr>
                        <tr>
                            <td>Defualt Unit ID</td>
                            <td><input class="form-control" name="def_unit" required></td>
                        </tr>
                        <tr>
                            <td>Units Stes</td>
                            <td><input class="form-control" name="units" required></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 mt-3">
                    <table class="table-sm table-bordered w-100">
                        <tbody>
                        <tr>
                            <td>
                                <label for="logo">Logo</label>
                                <div class="custom-file my-1">
                                    <input type="file" class="custom-file-input" id="logo" name="logo">
                                    <label class="custom-file-label" for="logo">Click to change</label>
                                </div>
                            </td>
                            <td>
                                <label for="dark_logo">Dark</label>
                                <div class="custom-file my-1">
                                    <input type="file" class="custom-file-input" id="dark_logo" name="dark_logo">
                                    <label class="custom-file-label" for="dark_logo">Click to change</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="mini_logo">Small</label>
                                <div class="custom-file my-1">
                                    <input type="file" class="custom-file-input" id="mini_logo" name="mini_logo">
                                    <label class="custom-file-label" for="mini_logo">Click to change</label>
                                </div>
                            </td>
                            <td>
                                <label for="favicon">Favicon</label>
                                <div class="custom-file my-1">
                                    <input type="file" class="custom-file-input" id="favicon" name="favicon">
                                    <label class="custom-file-label" for="favicon">Click to change</label>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="fRes" class="alert"></div>
            <div class="text-center mt-3 row">
                <button type="reset" class="btn btn-danger col-md-2 ml-5">Reset</button>
                <button type="submit" class="btn btn-success col-md-4 ml-5">Save</button>
            </div>
        </form>
    </div>

</section>

<style>
    .da-delDoc{display: none;}
</style>