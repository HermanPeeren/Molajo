<div id="content-box">
    <div id="element-box" class="login">
        <div class="m wbg">
            <h1><?php echo JText::_('COM_LOGIN_ADMINISTRATION_LOGIN') ?></h1>
            <jdoc:include type="message" />
            <jdoc:include type="component" />
            <p><?php echo JText::_('COM_LOGIN_VALID') ?></p>
            <p><a href="<?php echo JURI::root(); ?>"><?php echo JText::_('COM_LOGIN_RETURN_TO_SITE_HOME_PAGE') ?></a></p>
            <div id="lock"></div>
        </div>
    </div>
    <noscript>
        <?php echo JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
    </noscript>
</div>