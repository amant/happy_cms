<form action="" method="post" enctype="multipart/form-data">

<div class="col50">
    <fieldset>
        <legend>Employee Details</legend>    
          <table width="100%" border="0" cellspacing="2" cellpadding="2" class="paramlist">
            <tr>
              <td class="key">Employee Code</td>
              <td><input name="textfield3" type="text" id="textfield3" value="001245" /></td>
            </tr>
            <tr>
              <td class="key">First Name*</td>
              <td><input type="text" name="textfield" id="textfield" value="Venkat"/></td>
            </tr>
            <tr>
              <td class="key">Middle Name</td>
              <td><input name="textfield4" type="text" id="textfield4" value="Raman" /></td>
            </tr>
            <tr>
              <td class="key">Last Name*</td>
              <td><input name="textfield5" type="text" id="textfield5" value="Mukargee" /></td>
            </tr>
            <tr>
              <td class="key">Employee Type</td>
              <td><label>
                <select name="select3" id="select3">
                  <option value="1">Part Time</option>
                  <option value="2">Contract</option>
                  <option value="3" selected="selected">Permanent</option>
                  <option value="4">Volunteer/Intern</option>
                </select>
              </label></td>
            </tr>
            <tr>
              <td class="key">Join Date</td>
              <td><input name="textfield9" type="text" id="textfield9" value="1995-01-24 (yyyy-mm-dd)" /></td>
            </tr>
            <tr>
              <td class="key">End Date</td>
              <td><label>
                <input name="textfield11" type="text" id="textfield11" value="2015-01-24 (yyyy-mm-dd)" />
              </label></td>
            </tr>
            <tr>
              <td class="key">DOB/Age</td>
              <td><input name="textfield6" type="text" id="textfield6" value="1974-01-24 (yyyy-mm-dd)" /></td>
            </tr>
            <tr>
              <td class="key">Department</td>
              <td><select name="select2" id="select2">
                <option value="0">-Select-</option>
                <option value="1">Head Office</option>
                <option value="2" selected="selected">&nbsp;&nbsp;Administration</option>
                <option value="3">&nbsp;&nbsp;Human Resource</option>
                <option value="4">&nbsp;&nbsp;IT</option>
                <option value="5">&nbsp;&nbsp;Production</option>
                <option value="6">&nbsp;&nbsp;Sales</option>
                <option value="7">Brach Office - 1</option>
                <option value="8">&nbsp;&nbsp;Administration</option>
                <option value="9">&nbsp;&nbsp;Human Resource</option>
                <option value="10">&nbsp;&nbsp;Production</option>
                <option value="11">&nbsp;&nbsp;Sales</option>
                <option value="12">Brach Office - 2</option>
                <option value="13">&nbsp;&nbsp;Administration</option>
                <option value="14">&nbsp;&nbsp;Human Resource</option>
                <option value="15">&nbsp;&nbsp;Production</option>
                <option value="16">&nbsp;&nbsp;Sales</option>
              </select></td>
            </tr>
            <tr>
              <td class="key">Employee Post</td>
              <td><select name="select" id="select">
                <option value="0">-Select-</option>
                <option value="1" selected="selected">Manager</option>
                <option value="2">Director</option>
                <option value="3">IT</option>
                <option value="4">Sales</option>
                <option value="5">Clerk</option>
              </select>              </td>
            </tr>
            <tr>
              <td class="key">Gender</td>
              <td><input name="radio" type="radio" id="male" value="male" checked="checked" />
              Male 
                <input type="radio" name="radio" id="female" value="female" />
              Female</td>
            </tr>
            <tr>
              <td class="key">Upload Image</td>
              <td><label>
                <input type="file" name="fileField" id="fileField" />
              </label></td>
            </tr>
            <tr>
              <td class="key">Qualification</td>
              <td><label>
                <textarea name="textarea9" id="textarea9" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
            <tr>
              <td class="key">Description</td>
              <td><textarea name="textarea" id="textarea" cols="45" rows="5">Description
    </textarea></td>
            </tr>
          </table>
    </fieldset>	
    
    <?php if(get_active_controller() == 'admin'): ?>
    <fieldset>
      <legend>Misc</legend>    
          <table width="100%" border="0" cellspacing="2" cellpadding="2" class="paramlist">
            <tr>
              <td class="key">Last Promotion</td>
              <td><label>
                <textarea name="textarea8" id="textarea8" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
            <tr>
              <td class="key">Previous Department</td>
              <td><label>
                <textarea name="textarea7" id="textarea7" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
            <tr>
              <td class="key">Summary of Last Apprisal</td>
              <td><label>
                <textarea name="textarea6" id="textarea6" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
            <tr>
              <td class="key">Grivance Summary</td>
              <td><label>
                <textarea name="textarea2" id="textarea2" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
            <tr>
              <td class="key">Award Summary</td>
              <td><label>
                <textarea name="textarea3" id="textarea3" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
            <tr>
              <td class="key">Traning</td>
              <td><label>
                <textarea name="textarea4" id="textarea4" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
            <tr>
              <td class="key">Current Salary</td>
              <td><label>
                <input type="text" name="textfield10" id="textfield10" />
              </label></td>
            </tr>
            <tr>
              <td class="key">Benifits</td>
              <td><label>
                <textarea name="textarea5" id="textarea5" cols="45" rows="5"></textarea>
              </label></td>
            </tr>
          </table>
    </fieldset>	
    <?php endif; ?>
    
</div>
<div class="col50">
    <fieldset>
        <legend>Address Details</legend>    
          <table width="100%" border="0" cellspacing="2" cellpadding="2" class="paramlist">
            <tr>
              <td class="key">Address</td>
              <td><textarea name="textarea" id="textarea" cols="45" rows="5">Description
    </textarea></td>
            </tr>
            <tr>
              <td class="key">Postal</td>
              <td><input name="textfield2" type="text" id="textfield2" value="1202" /></td>
            </tr>
            <tr>
              <td class="key">Phone</td>
              <td><input type="text" name="textfield7" id="textfield7" /></td>
            </tr>
            <tr>
              <td class="key">Phone Extension</td>
              <td><label>
                <input type="text" name="textfield12" id="textfield12" />
              </label></td>
            </tr>
            <tr>
              <td class="key">Email</td>
              <td><input type="text" name="textfield8" id="textfield8" /></td>
            </tr>
          </table>
    </fieldset>	
</div>


</form>
<div class="clear">&nbsp;</div>