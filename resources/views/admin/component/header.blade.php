@props(['title'=>'Overview','sub_title'=>'Dashboard'])

<div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <!-- Page pre-title -->
          <div class="page-pretitle">
            {{ $title }}
          </div>
          <h2 class="page-title">
            {{ $sub_title }}
          </h2>
        </div> 
      </div>
    </div>
  </div>