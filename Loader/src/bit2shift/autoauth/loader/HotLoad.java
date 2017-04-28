package bit2shift.autoauth.loader;

import java.io.File;
import java.util.List;

import net.minecraft.launchwrapper.ITweaker;
import net.minecraft.launchwrapper.LaunchClassLoader;

public class HotLoad implements ITweaker
{
	public void acceptOptions(List<String> args, File gameDir, File assetsDir, String profile)
	{
	}

	public void injectIntoClassLoader(LaunchClassLoader classLoader)
	{
	}

	public String getLaunchTarget()
	{
		return "";
	}

	public String[] getLaunchArguments()
	{
		return new String[0];
	}
}
