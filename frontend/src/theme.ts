import {createMuiTheme, SimplePaletteColorOptions} from "@material-ui/core"
import {PaletteOptions} from "@material-ui/core/styles/createPalette"
import {green, red} from "@material-ui/core/colors"

const palette: PaletteOptions = {
	primary: {
		main: '#79aec8',
		contrastText: '#fff',
		dark: '#459ac4'
	},
	secondary: {
		main: '#4db5ab',
		contrastText: '#fff',
		dark: '#055a52'
	},
	background: {
		default: '#fafafa'
	},
	error: {
		main: red[500]
	},
	success: {
		main: green[500],
		contrastText: '#fff'
	}
}

const theme = createMuiTheme({
	palette,
	overrides: {
		MUIDataTable: {
			paper: {
				boxShadow: 'none'
			}
		},
		MUIDataTableToolbar: {
			root: {
				minHeight: '58px',
				backgroundColor: palette!.background!.default,
			},
			icon: {
				color: (palette!.primary! as SimplePaletteColorOptions).main,
				'&:hover, &:active, &.focus': {
					color: (palette!.secondary! as SimplePaletteColorOptions).dark,
				}
			},
			iconActive: {
				color: (palette!.secondary! as SimplePaletteColorOptions).dark,
				'&:hover, &:active, &.focus': {
					color: (palette!.secondary! as SimplePaletteColorOptions).dark,
				}
			}
		},
		MUIDataTableHeadCell: {
			fixedHeader: {
				paddingBottom: 8,
				paddingTop: 8,
				backgroundColor: (palette!.primary! as SimplePaletteColorOptions).main,
				color: '#fff',
				'&[aria-sort]': {
					backgroundColor: (palette!.primary! as SimplePaletteColorOptions).dark
				}
			},
			sortActive: {
				color: '#fff'
			},
			sortAction: {
				alignItems: 'center'
			},
			sortLabelRoot: {
				'& svg':{
					color: '#fff !important'
				}
			}
		},
		MUIDataTableSelectCell:{
			headerCell: {
				backgroundColor: (palette!.primary! as SimplePaletteColorOptions).main,
				'& span':{
					color: '#fff !important'
				}
			}
		},
		MUIDataTableBodyCell: {
			root:{
				color: (palette!.secondary! as SimplePaletteColorOptions).main,
				'&hover, &:active, &.focus':{
					color: (palette!.secondary! as SimplePaletteColorOptions).main,
				}
			}
		},
		MUIDataTableToolbarSelect:{
			title:{
				color: (palette!.primary! as SimplePaletteColorOptions).main,
			},
			iconButton: {
				color: (palette!.primary! as SimplePaletteColorOptions).main,
			}
		},
		MUIDataTableBodyRow:{
			root:{
				'&:nth-child(odd)':{
					backgroundColor: palette!.background!.default,
				}
			}
		},
		MUIDataTablePagination:{
			root: {
				color: (palette!.primary! as SimplePaletteColorOptions).main,
			}
		}
	}
});

export default theme;