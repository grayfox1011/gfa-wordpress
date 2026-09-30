/* GFA — blocchi dinamici e formati per l'editor (senza build: API globali di WordPress). */
(function (wp) {
  'use strict';
  var el = wp.element.createElement;
  var ServerSideRender = wp.serverSideRender;
  var useBlockProps = wp.blockEditor.useBlockProps;

  (window.gfaBlocks || []).forEach(function (b) {
    wp.blocks.registerBlockType(b.name, {
      apiVersion: 3,
      title: b.title,
      description: b.description,
      icon: b.icon,
      category: 'gfa',
      supports: { html: false },
      edit: function () {
        return el('div', useBlockProps(), el(ServerSideRender, { block: b.name }));
      },
      save: function () { return null; }
    });
  });

  // Formato "Da fornire": evidenzia un dato che GFA deve ancora confermare.
  function toggleButton(name, title, icon) {
    return function (props) {
      return el(wp.blockEditor.RichTextToolbarButton, {
        icon: icon,
        title: title,
        isActive: props.isActive,
        onClick: function () { props.onChange(wp.richText.toggleFormat(props.value, { type: name })); }
      });
    };
  }
  wp.richText.registerFormatType('gfa/da-fornire', {
    title: 'Da fornire', tagName: 'mark', className: 'todo', edit: toggleButton('gfa/da-fornire', 'Da fornire', 'warning')
  });
  wp.richText.registerFormatType('gfa/evidenzia', {
    title: 'Evidenziatore giallo', tagName: 'mark', className: 'hl', edit: toggleButton('gfa/evidenzia', 'Evidenziatore giallo', 'admin-customizer')
  });
})(window.wp);
