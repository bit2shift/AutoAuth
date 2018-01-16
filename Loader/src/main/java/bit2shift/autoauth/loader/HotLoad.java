package bit2shift.autoauth.loader;

import java.io.File;
import java.util.List;

import cpw.mods.fml.common.Mod;
import cpw.mods.fml.common.Mod.EventHandler;
import cpw.mods.fml.common.event.FMLInitializationEvent;
import cpw.mods.fml.common.event.FMLPostInitializationEvent;
import cpw.mods.fml.common.event.FMLPreInitializationEvent;
import cpw.mods.fml.common.eventhandler.SubscribeEvent;
import cpw.mods.fml.common.network.FMLNetworkEvent.ServerConnectionFromClientEvent;
import net.minecraft.launchwrapper.IClassTransformer;
import net.minecraft.launchwrapper.ITweaker;
import net.minecraft.launchwrapper.LaunchClassLoader;

@Mod(modid = "hotload")
public class HotLoad implements ITweaker, IClassTransformer
{
	public void acceptOptions(List<String> args, File gameDir, File assetsDir, String profile)
	{
	}

	public void injectIntoClassLoader(LaunchClassLoader classLoader)
	{
		Package p = this.getClass().getPackage();
		System.err.printf("Spec: %s\tImpl: %s\n", p.getSpecificationVersion(), p.getImplementationVersion());
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
		//ClassReader reader = new ClassReader(data);
		//System.err.printf("obf: [%-64s] name: [%-64s] realname: [%-64s]\n", name, transformedName, reader.getClassName());
		return data;
	}

	@EventHandler
	public void fml(FMLPreInitializationEvent event)
	{
		System.err.println("HOTLOAD pre-init");
	}

	@EventHandler
	public void fml(FMLInitializationEvent event)
	{
		System.err.println("HOTLOAD init");
	}

	@EventHandler
	public void fml(FMLPostInitializationEvent event)
	{
		System.err.println("HOTLOAD post-init");
	}

	@SubscribeEvent
	public void derp(ServerConnectionFromClientEvent event)
	{
		System.err.println("HOTLOAD: " + event);
	}
}
