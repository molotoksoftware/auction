<!-- My Modal HTML -->
<div id="mymodal" style="display: none;">
    <?php
    Yii::import('application.modules.gallery.components.GalleryManager.models.Gallery');
    

    echo CHtml::dropDownList('gallery', 0, CHtml::listData(Gallery::model()->findAll(), 'id', 'name'));
    ?>
    <section>
        <p><button id="mymodal-link">Insert</button></p>
    </section>
    <footer>
        <a href="#" class="redactor_modal_btn redactor_btn_modal_close">Close</a>
    </footer>
</div>