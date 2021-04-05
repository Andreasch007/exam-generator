<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class='nav-item'><a class='nav-link' href="{{ backpack_url('exam') }}"><i class="nav-icon las la-file-alt"></i> Exams</a></li>
<li class='nav-item'><a class='nav-link' href="{{ backpack_url('question') }}"><i class='nav-icon la la-question'></i> Questions</a></li>
<li class='nav-item'><a class='nav-link' href="{{ backpack_url('category') }}"><i class='nav-icon la la-tag'></i> Categories</a></li>
<li class='nav-item'><a class='nav-link' href="{{ backpack_url('taskheader') }}"><i class="nav-icon las la-tasks"></i></i> Tasks</a></li>


@if(backpack_user()->hasRole('Super Admin||Admin'))
<li class='nav-item'><a class='nav-link' href="{{ backpack_url('company') }}"><i class="nav-icon las la-building"></i> Company</a></li>
<li class='nav-item'><a class='nav-link' href="{{ backpack_url('journalexam') }}"><i class="nav-icon las la-journal-whills"></i></i> Journal Exams</a></li>
<li class='nav-item'><a class='nav-link' href="{{ backpack_url('UserApproval') }}"><i class='nav-icon la la-user'></i> User Approval</a></li>
@endif
@if(backpack_user()->hasRole('Super Admin'))
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
	<ul class="nav-dropdown-items">
	  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
	  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
	  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
	  
	</ul>
</li>

@endif
<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('answer') }}'><i class='nav-icon la la-question'></i> Answers</a></li> -->

<!-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('taskdetail') }}'><i class='nav-icon la la-question'></i> TaskDetails</a></li> -->
