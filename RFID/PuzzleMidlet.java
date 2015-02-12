import java.io.IOException;

import javax.microedition.lcdui.Command;
import javax.microedition.lcdui.CommandListener;
import javax.microedition.lcdui.Display;
import javax.microedition.lcdui.Displayable;
import javax.microedition.midlet.MIDlet;

public class PuzzleMidlet extends MIDlet implements CommandListener {

	private Display display;
	private Command exitCommand;
	private Command newGameCommand;
	private PuzzleCanvas gameCanvas;

	public PuzzleMidlet() throws InterruptedException, IOException {

		exitCommand = new Command("Exit", Command.CANCEL, 1);
		newGameCommand = new Command("New Game", Command.OK, 1);

		gameCanvas = new PuzzleCanvas(false, true);
	}

	protected void destroyApp(boolean arg0) {

		notifyDestroyed();
	}

	protected void pauseApp() {

		notifyPaused();
	}

	protected void startApp() {

		gameCanvas.addCommand(exitCommand);
		gameCanvas.addCommand(newGameCommand);

		gameCanvas.setCommandListener((CommandListener) this);

		display = Display.getDisplay(this);

		display.setCurrent(gameCanvas);
	}

	public void commandAction(Command c, Displayable d) {

		try {

			if (c.getLabel().equals("Exit")) {

				gameCanvas.stopGame();

				this.destroyApp(true);
			} else if (c.getLabel().equals("New Game")) {

				gameCanvas.newGame();
			}
		} catch (InterruptedException e) {

			e.printStackTrace();
		}
	}
}
