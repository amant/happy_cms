<ul>
  <li>
    <?php echo anchor('admin/login', 'Login Page (Administrator)')?>
    <ul>
      <li>
        <?php echo anchor('admin/dashboard', 'Dashboard')?>
      </li>
      <li>
        <?php echo anchor('admin/kpi_index', 'Appraisal Module')?>
        <ul>
          <li>
            <?php echo anchor('admin/appraisal_deployment', 'Initiate Appraisal')?>
          </li>
          <li>
            <?php echo anchor('admin/appraisal_track', 'Track Appraisal')?>
          </li>
          <li>
            <?php echo anchor('admin/appraisal_form_add', 'Add Appraisal Form')?>
          </li>
          <li>
            <?php echo anchor('admin/appraisal_form_list', 'List Appraisal Form')?>
          </li>
          <li>
            <?php echo anchor('admin/kpi_group_add', 'Add KPI')?>
          </li>
          <li>
            <?php echo anchor('admin/kpi_group_list', 'List KPI')?>
          </li>
        </ul>
      </li>
      <li>
        <?php echo anchor('admin/login', 'Employee')?>
        <ul>
          <li>
            <?php echo anchor('admin/employee_add', 'Add Employee')?>
          </li>
          <li>
            <?php echo anchor('admin/employee_list', 'List Employee')?>
          </li>
          <li>
            <?php echo anchor('admin/department_list', 'Define Department')?>
          </li>
          <li>
            <?php echo anchor('admin/job_list', 'Define Job Position')?>
          </li>
        </ul>
      </li>
      <li>
        <?php echo anchor('admin/report_list', 'Report')?>
      </li>
      <li>
        <?php echo anchor('admin/setting', 'Settings')?>
      </li>
    </ul>
  </li>
  <li>
    <?php echo anchor('user/login', 'Login Page (User)') ?>
    <ul>
      <li>
        <?php echo anchor('user/dashboard', 'Home') ?>
      </li>
      <li>
        <?php echo anchor('user/survey', 'Survey') ?>
      </li>
      <li>
        <?php echo anchor('user/profile', 'Profile') ?>
      </li>
      <li>
        <?php echo anchor('user/setting', 'Settings')?>
      </li>
    </ul>
  </li>
</ul>
