import * as React from 'react';
import {Button, ButtonProps} from "@material-ui/core"
import { Box } from '@material-ui/core';
import {makeStyles, Theme} from "@material-ui/core/styles"

const useStyles = makeStyles((theme: Theme) => {
	return {
		submit: {
			margin: theme.spacing(0.5)
		}
	}
})

interface SubmitActionProps {
	disabledButtons?: boolean;
	handleSave: () => void;
}

const SubmitActions: React.FC<SubmitActionProps> = (props) => {

	const classes = useStyles();

	const buttonProps: ButtonProps = {
		className: classes.submit,
		variant: 'contained',
		color: 'secondary',
		disabled: props.disabledButtons === undefined ? false : props.disabledButtons,
	}

	return (
		<Box dir={"rtl"}>
			<Button {...buttonProps} onClick={props.handleSave}>
				Salvar
			</Button>

			<Button {...buttonProps} type="submit">
				Salvar e continuar editando
			</Button>
		</Box>
	);
};

export default SubmitActions;