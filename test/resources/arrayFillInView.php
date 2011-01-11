<!-- Test some array named checkboxes first -->
<input type="checkbox" id="<?php echo $this->id('ids_1')?>" name="<?php echo $this->name('ids[]') ?>" value="on" />
<input type="checkbox" id="<?php echo $this->id('ids_2')?>" name="<?php echo $this->name('ids[]') ?>" value="on" />
<input type="checkbox" id="<?php echo $this->id('ids_3')?>" name="<?php echo $this->name('ids[]') ?>" value="on" />

<input type="text" id="<?php echo $this->id('test_name') ?>" name="<?php echo $this->name('test[name]') ?>" value="" />

<textarea id="<?php echo $this->id('test_address') ?>" name="<?php echo $this->name('test[address]') ?>"></textarea>