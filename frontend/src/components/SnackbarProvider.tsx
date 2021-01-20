import * as React from 'react';

import {SnackbarProvider as NotistackProvider, SnackbarProviderProps, WithSnackbarProps} from "notistack"
import {IconButton} from "@material-ui/core"
import CloseIcon from '@material-ui/icons/Close'

import {makeStyles, Theme} from "@material-ui/core/styles"

const useStyles = makeStyles((theme:Theme) => {
	return {
		variantSuccess: {
			backgroundColor: theme.palette.success.main,
		},
		variantError: {
			backgroundColor: theme.palette.error.main,
		},
		variantInfo: {
			backgroundColor: theme.palette.primary.main,
		}
	}
})

export const SnackbarProvider: React.FC<SnackbarProviderProps> = (props: SnackbarProviderProps) => {

	let snackbarProviderRef: WithSnackbarProps;
	const classes = useStyles();

	const defaultProps: Partial<SnackbarProviderProps> = {
		classes,
		autoHideDuration: 3000,
		maxSnack: 3,
		anchorOrigin: {
			horizontal: 'right',
			vertical: 'top',
		},
		ref: (el:any) => (snackbarProviderRef = el),
		action: (key:any) => (
			<IconButton
				color={"inherit"}
				style={{fontSize: 20}}
				onClick={() => snackbarProviderRef.closeSnackbar(key)}
			>
				<CloseIcon/>
			</IconButton>
		)
	}

	const newProps = {...defaultProps, ...props}

	return (
		<NotistackProvider {...newProps}>
			{props.children}
		</NotistackProvider>
	);
};