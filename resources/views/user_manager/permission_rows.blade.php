<thead>
	<tr>
		<th style="padding:10px 20px;background:#E0ECFF;">Section</th>
		<th colspan="5" style="padding:10px 20px;background:#E0ECFF;  ">Operation</th>
	</tr>
</thead>

<tbody>

  @if(user()->HasRole('Super'))
	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_activity_log">Activity Log</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_activity_log">View</div>
		</td>
	</tr>
	@endif

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_app_setting">App settings</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_app_setting">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_app_setting">Edit</div>
      @if(user()->HasRole('Super'))
			<div><input type="checkbox" class="permission-checkbox" name="create_app_setting">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_app_setting">Remove</div>
			@endif
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_control_center">Control Center</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_control_center">View</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_user">Users</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_user">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_user">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_user">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_user">Remove</div>
			<div><input type="checkbox" class="permission-checkbox" name="toggle_user_status">Toggle User Status</div>
			<div><input type="checkbox" class="permission-checkbox" name="reset_user_password">Reset User PWD</div>
			<div><input type="checkbox" class="permission-checkbox" name="impersonate_user">Impersonate Others</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_roles_and_permissions">Roles & Permissions
		</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_roles_and_permissions">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_role">Add Role</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_role">Edit Role Name</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_role">Remove Role</div>
			<div><input type="checkbox" class="permission-checkbox" name="assign_permission_to_role">Add Permission to Role</div>
			<div><input type="checkbox" class="permission-checkbox" name="assign_permission_to_user">Assign Permission to User</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_permissions_description">Perm. Desc.</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_permissions_description">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_permissions_description">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_permissions_description">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_permissions_description">Remove</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_permission_request">Permission Requests</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_permission_request">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_permission_request">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_permission_request">Edit</div>
			<!-- <div><input type="checkbox" class="permission&#45;checkbox" name="destroy_permission_request">Remove</div> -->
			<div><input type="checkbox" class="permission-checkbox" name="approve_permission_request">Approve</div>
			<div><input type="checkbox" class="permission-checkbox" name="reject_permission_request">Reject</div>
			<div><input type="checkbox" class="permission-checkbox" name="grant_permission_request">Grant</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_change_request">Change Requests</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_change_request">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_change_request">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_change_request">Edit</div>
			<!-- <div><input type="checkbox" class="permission&#45;checkbox" name="destroy_change_request">Remove</div> -->
			<div><input type="checkbox" class="permission-checkbox" name="approve_change_request">Approve</div>
			<div><input type="checkbox" class="permission-checkbox" name="reject_change_request">Reject</div>
			<div><input type="checkbox" class="permission-checkbox" name="grant_change_request">Grant</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="review_permissions">Review Permissions</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="review_permissions">Review Permissions</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="review_roles">Review Role</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="review_roles">Review Roles</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_product">Products</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_product">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_product">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_product">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="disable_or_enable_product">Toggle Product</div>
			<div><input type="checkbox" class="permission-checkbox" name="toggle_product_blended">Toggle Product Blended</div>
			<div><input type="checkbox" class="permission-checkbox" name="hide_product_regapedan">Hide Regapedan</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_product">Remove</div>
		</td>
	</tr>


	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_brand">Brands</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_brand">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_brand">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_brand">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_brand">Remove</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_category">Categories</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_category">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_category">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_category">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_category">Remove</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_customs_product">Customs Products</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_customs_product">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_customs_product">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_customs_product">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_customs_product">Remove</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_product">Products</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_product">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_product">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_product">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="disable_or_enable_product">Toggle Product</div>
			<div><input type="checkbox" class="permission-checkbox" name="hide_product_regapedan">Hide Regapedan</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_product">Remove</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_product_report_template">Update Report Template</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_product_check_type">Product Tests</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_product_check_type">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_product_check_type">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_product_check_type">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_product_check_type">Remove</div>
		</td>
	</tr>


	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_check_type">Test Types</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_check_type">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_check_type">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_check_type">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_check_type">Remove</div>
			<div><input type="checkbox" class="permission-checkbox" name="disable_or_enable_check_types">Toggle (Enable |
				Disable)</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_specification">Specifications</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_specification">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="view_all_specification">View (All Statuses)</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_specification">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_specification">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_specification">Remove</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="other_permissions">Other</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="see_cleint_prints">Can See Client Print Buttons
			</div>
		</td>
	</tr>



	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_custom">Customs</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_custom">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="receive_transaction">Receive Transaction</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_country">Countries</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_country">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_country">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_country">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_country">Remove</div>
		</td>
	</tr>

	<tr>
		<td><input type="checkbox" main class="permission-checkbox" name="view_department">Departments</td>
		<td>
			<div><input type="checkbox" view class="permission-checkbox" name="view_department">View</div>
			<div><input type="checkbox" class="permission-checkbox" name="create_department">Add</div>
			<div><input type="checkbox" class="permission-checkbox" name="update_department">Edit</div>
			<div><input type="checkbox" class="permission-checkbox" name="destroy_department">Remove</div>
		</td>
	</tr>

<!-- bread_permissions -->
</tbody>
