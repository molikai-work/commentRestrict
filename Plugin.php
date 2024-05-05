<?php
/**
 * 限制評論字數和內容
 * @author molikai
 * @package CommentRestrict
 * @version 1.1.0
 * @link https://github.com/molikai-work/commentRestrict
 */

class CommentRestrict_Plugin implements Typecho_Plugin_Interface {
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Feedback')->comment = array('CommentRestrict_Plugin', 'checkComment');
        return _t('插件已成功激活');
    }

    public static function deactivate()
    {
        return _t('插件已停用');
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $minLength = new Typecho_Widget_Helper_Form_Element_Text('minLength', NULL, '5', _t('評論最少字數'));
        $form->addInput($minLength);

        $maxLength = new Typecho_Widget_Helper_Form_Element_Text('maxLength', NULL, '200', _t('評論最多字數'));
        $form->addInput($maxLength);

        $forbiddenWords = new Typecho_Widget_Helper_Form_Element_Text('forbiddenWords', NULL, 'ces1,敏感詞2,#@$&3', _t('違禁詞列表（使用逗號分隔）'));
        $form->addInput($forbiddenWords);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form) {
        // ...
    }

    public static function checkComment($comment, $post) {
            $options = Typecho_Widget::widget('Widget_Options')->plugin('CommentRestrict');

            $minLength = intval($options->minLength);
            $maxLength = intval($options->maxLength);

            $forbiddenWords = array_map('trim', explode(',', $options->forbiddenWords));

            $content = $comment['text'];
            $contentLength = mb_strlen($content, 'utf-8');

            if ($contentLength < $minLength || $contentLength > $maxLength) {
               throw new Typecho_Widget_Exception(_t('評論字數不符合要求，請輸入 %d-%d 字之間的評論.', $minLength, $maxLength));
            }

            foreach ($forbiddenWords as $word) {
                if (!empty($word) && mb_stripos($content, $word, 0, 'utf-8') !== false) {
                    throw new Typecho_Widget_Exception(_t('評論中包含違禁詞，請檢查並修改。'));
            }
        }

        return $comment;
    }
}
