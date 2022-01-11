<div class="container mt-3">
    <?php 
        if(isset($_POST['form_name'])) {
            if(isset($submit)){
                call_user_func($submit);
            } else{
                echo '<div class="alert alert-warning" role="alert">';
                    echo 'Oops, something was broken...';
                echo '</div>';
            }
        }

    ?>

    <h1>
        <?php echo isset($page_title) ? $page_title : "Title"; ?>
    </h1>

    <ul class="nav nav-tabs bg-faded" id="<?php echo md5(isset($page_title) ? $page_title : "Title"); ?>" role="tablist">
        <?php 
            if( isset($section_header) ) echo $section_header;
            else { 
        ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="section_1-tab" data-bs-toggle="tab" data-bs-target="#section_1" type="button" role="tab" aria-controls="section_1" aria-selected="true">Section 1</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="section_2-tab" data-bs-toggle="tab" data-bs-target="#section_2" type="button" role="tab" aria-controls="section_2" aria-selected="false">Section 2</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="section_3-tab" data-bs-toggle="tab" data-bs-target="#section_3" type="button" role="tab" aria-controls="section_3" aria-selected="false">Section 3</button>
            </li>
        <?php
            } 
        ?>
    </ul>
    <div class="tab-content" id="<?php echo md5(isset($page_title) ? $page_title : "Title"); ?>_Content">
        <?php 
            if( isset($section_content) ) echo $section_content;
            else { 
        ?>
            <div class="tab-pane fade show active" id="section_1" role="tabpanel" aria-labelledby="section_1-tab">
                <h1>section_1</h1>
            </div>
            <div class="tab-pane fade" id="section_2" role="tabpanel" aria-labelledby="section_2-tab">
                <h1>section_2</h1>
            </div>
            <div class="tab-pane fade" id="section_3" role="tabpanel" aria-labelledby="section_3-tab">
                <h1>section_3</h1>
            </div>
        <?php
            } 
        ?>
    </div>

</div>