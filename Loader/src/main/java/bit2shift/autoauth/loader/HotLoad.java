package bit2shift.autoauth.loader;

import java.io.File;
import java.util.List;

import org.objectweb.asm.ClassReader;

import net.minecraft.launchwrapper.IClassTransformer;
import net.minecraft.launchwrapper.ITweaker;
import net.minecraft.launchwrapper.LaunchClassLoader;
import net.minecraft.launchwrapper.LogWrapper;

public class HotLoad implements ITweaker, IClassTransformer
{
	public void acceptOptions(List<String> args, File gameDir, File assetsDir, String profile)
	{
	}

	public void injectIntoClassLoader(LaunchClassLoader classLoader)
	{
		Package p = this.getClass().getPackage();
		LogWrapper.info("Spec: %s\tImpl: %s", p.getSpecificationVersion(), p.getImplementationVersion());

		classLoader.registerTransformer(this.getClass().getName());
	}

	public String getLaunchTarget()
	{
		return "";
	}

	public String[] getLaunchArguments()
	{
		return new String[0];
	}

	public byte[] transform(String name, String transformedName, byte[] data)
	{
		ClassReader reader = new ClassReader(data);
		LogWrapper.info("obf: [%-96s] name: [%-96s] realname: [%-96s]", name, transformedName, reader.getClassName());
		return data;
	}
}
