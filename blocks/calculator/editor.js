( function ( blocks, element, blockEditor ) {
	if ( blocks.getBlockType( 'bomcas/calculator' ) ) {
		return;
	}
	var el = element.createElement;
	var useBlockProps = blockEditor.useBlockProps;
	blocks.registerBlockType( 'bomcas/calculator', {
		apiVersion: 3,
		title: 'BOMCAS Tax Calculators – Canada',
		category: 'widgets',
		description: 'Canadian income tax, sales tax, or a mortgage payment. Payroll calculators are in the Professional plan, not in this block.',
		attributes: {
			calculator: { type: 'string', default: 'sales-tax' },
			province: { type: 'string', default: '' }
		},
		edit: function ( props ) {
			var blockProps = useBlockProps();
			var ServerSideRender = window.wp.serverSideRender;
			if ( ServerSideRender ) {
				return el( 'div', blockProps, el( ServerSideRender, {
					block: 'bomcas/calculator',
					attributes: props.attributes
				} ) );
			}
			return el( 'div', blockProps, el( 'p', null, 'BOMCAS Tax Calculators – Canada' ) );
		},
		save: function () {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor );
